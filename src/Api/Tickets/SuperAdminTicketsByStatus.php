<?php

namespace Supp\Api\Tickets;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class SuperAdminTicketsByStatus implements TicketsByStatusInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getTicketsByStatus(ServerRequestInterface $request, array $routeArgs = [])
    {
        return $this->getTicketsByStatusData($routeArgs['filter']);
    }

    private function getTicketsByStatusData($filter)
    {
        $sql = <<<SQL
SELECT  t.id as id,
        t.title,
        tt.name as ticket_type,
        tc.name as category,
        CONCAT(u.first, " ", u.last) as created_by,
        CONCAT(u2.first, " ", u2.last) as assignee,
        ts.name as status
FROM tickets t
JOIN ticket_types tt on t.ticket_type = tt.id
JOIN ticket_categories tc on t.category = tc.id
JOIN ticket_status ts on t.status = ts.id
JOIN users u on t.created_by = u.id
LEFT JOIN users u2 on t.assignee = u2.id
WHERE t.status=:status;
SQL;
        $values = ["status" => $filter];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}