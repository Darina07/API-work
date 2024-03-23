<?php

namespace Supp\Api\Projects;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class SuperAdminProjectsBilling implements ProjectsBillingInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function getProjectsBilling(ServerRequestInterface $request, array $routeArgs = [])
    {
        $searchParam = $this->getSearchParam($request);
        $data = $this->getProjectsBillings($searchParam);
        return $data;
    }

    private function getProjectsBillings($searchParam)
    {
        $result = explode("/",$searchParam);

        $sql = <<<SQL
SELECT p.id as project_id, p.name as project, SEC_TO_TIME(SUM(b.billed)) as billed
FROM projects p
JOIN billings b on p.id = b.project
WHERE b.created_on < :end_date and b.created_on > :start_date
GROUP BY p.id;
SQL;

        $values = [
            'end_date' => $result[1],
            'start_date' => $result[0]
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSearchParam(ServerRequestInterface $request)
    {
        $user_query = urldecode($request->getQueryParams()['q']);
        $user_query = filter_var($user_query, FILTER_SANITIZE_STRING);
        $user_query = trim($user_query);

        return $user_query;
    }
}