<?php

namespace Supp\Api\Clients;

use DateTime;
use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ServerRequestInterface;

class AdminNewCompany implements NewCompanyInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    public function saveNewCompany(ServerRequestInterface $request, array $routeArgs = [])
    {
        $requestObj = json_decode($request->getBody()->getContents(), true);
        $client_id = $this->postNewCompany($requestObj);
        return $this->loadData($client_id);
    }

    private function postNewCompany($requestObj)
    {
        $sql = <<<SQL
INSERT INTO clients(name, created_at) VALUES (:name, :today);
SQL;
        $today = clone $this->container->get(DateTime::class);
        $values = [
            'name' => $requestObj['company_name'],
            'today' => (clone $today)->format('Y-m-d')
        ];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values)->run();
        return $runner->lastInsertId();
    }

    private function loadData($client_id)
    {
        $sql = <<<SQL
SELECT * FROM clients WHERE id=:client_id;
SQL;
        $values = [
            'client_id' => $client_id
        ];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}