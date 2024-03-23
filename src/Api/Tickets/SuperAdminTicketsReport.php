<?php

namespace Supp\Api\Tickets;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class SuperAdminTicketsReport implements TicketsReportInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getTicketsReport(ServerRequestInterface $request, array $routeArgs = [])
    {
        $data['categories'] = $this->getTicketsReportCategoryData();
        $data['statuses'] = $this->getTicketsReportStatusData();
        return $data;
    }

    private function getTicketsReportCategoryData()
    {
        $sql = <<<SQL
SELECT tc.name, (SELECT COUNT(id) FROM tickets WHERE category=tc.id) as tickets_number FROM ticket_categories tc;
SQL;
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $stmt = $runner->run();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTicketsReportStatusData()
    {
        $sql = <<<SQL
SELECT ts.name, (SELECT COUNT(id) FROM tickets WHERE status=ts.id) as tickets_number FROM ticket_status ts;
SQL;
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $stmt = $runner->run();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}