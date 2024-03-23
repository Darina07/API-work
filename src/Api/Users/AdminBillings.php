<?php

namespace Supp\Api\Users;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Models\Billing;
use Supp\Api\Time\TimeConverter;

class AdminBillings implements UserBillingsInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getBillings(ServerRequestInterface $request, array $routeArgs = []): array
    {
        $data = $this->getActiveUserBillingData($routeArgs['id']);
        $data['billings'] = $this->getUserBillingData($routeArgs['id'], $request);
        return $data;
    }

    private function getUserBillingData($userID, $request)
    {
        $sql = <<<SQL
SELECT b.id,b.project as project_id, p.name as project, b.billed, b.created_on, b.completed_date, b.completed,b.description
FROM billings b
JOIN projects p on p.id = b.project
WHERE user=:user_id AND b.completed=1
ORDER BY STR_TO_DATE(created_on, '%Y-%m-%d %H:%i:%s') DESC, STR_TO_DATE(completed_date, '%Y-%m-%d %H:%i:%s') DESC;
SQL;

        $values = ['user_id' => $userID];
        $runner = $this->container->get(QueryRunner::class);
        $stmt = $runner->useQuery($sql)->withValues($values)->run();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $k => $v) {
            $userIP = isset($request->getServerParams()['REMOTE_ADDR']) ? $request->getServerParams()['REMOTE_ADDR'] : $request->getServerParams()['SERVER_ADDR'];
            $timeConverter = new TimeConverter($this->container, $userIP);

            $result[$k]['created_on_user_timezone'] = $timeConverter->convertToUserTimezone($v['created_on']);
            $result[$k]['completed_date_user_timezone'] = $timeConverter->convertToUserTimezone($v['completed_date']);
        }

        return $result;
    }

    private function getActiveUserBillingData($userID)
    {
        $activeBillingData = Billing::checkForActiveBillingExisting($this->container, $userID);

        $data = [];

        if (!$activeBillingData) {
            $data['active'] = 0;
            $data['active_id'] = null;
            $data['project_id'] = 0;
            return $data;
        }

        $data['active'] = strtotime(date('Y-m-d H:i:s')) - strtotime($activeBillingData->created_on);
        $data['active_id'] = $activeBillingData->id;
        $data['project_id'] = $activeBillingData->project;

        return $data;
    }
}
