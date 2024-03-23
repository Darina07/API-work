<?php

namespace Supp\Api\Statuses;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class SuperAdminStatuses implements StatusInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function getStatuses(ServerRequestInterface $request, array $routeArgs = [])
    {
        $sql = <<<SQL
select id, name FROM ticket_status;
SQL;

        $values = [];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
