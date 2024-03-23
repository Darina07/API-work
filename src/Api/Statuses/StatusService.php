<?php

namespace Supp\Api\Statuses;

use Exception;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class StatusService extends SuppApiService
{
    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = StatusServiceFactory::getService($this->container);
            $data = $service->getStatuses($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }

        return new JsonResponse($data, 200);
    }
}
