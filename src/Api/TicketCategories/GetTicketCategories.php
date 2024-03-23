<?php

namespace Supp\Api\TicketCategories;

use Exception;
use Hph\Api\ApiService;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\SuppApiService;
use PDO;
class GetTicketCategories extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $runner = $this->container->get(QueryRunner::class);
            $stmt = $runner->useQuery($this->query())->withValues($this->values())->run();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return $this->returnException($e);
        }
        return new JsonResponse($data, 200);
    }
    private function query()
    {
        return <<<SQL
select *
from ticket_categories;
SQL;
    }

    private function values()
    {
        return [];
    }
}