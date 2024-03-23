<?php

namespace Supp\Api\Clients;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class AdminClients implements ClientServiceInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    public function getAllClients(ServerRequestInterface $request, array $routeArgs = [])
    {
        return $this->getAllClientsData();
    }

    private function getAllClientsData()
    {
        $sql = <<<SQL
SELECT id, name, created_at FROM clients ORDER BY name;
SQL;
        $values = [];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}