<?php

namespace Supp\Api\Faqs;

use Exception;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class FaqService extends SuppApiService
{
    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $data = $this->getFaq();
        } catch (Exception $e) {
            return $this->returnException($e);
        }

        return new JsonResponse($data,200);
    }

    private function getFaq()
    {
        $sql = <<<SQL
select id, title, description from faq;
SQL;
        $values = [];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql);
        $runner->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}