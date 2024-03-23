<?php

namespace Supp\Api\Projects;

use Exception;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class ProjectsBillingService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = ProjectsBillingServiceFactory::getService($this->container);
            $data = $service->getProjectsBilling($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }

        return new JsonResponse($data, 200);
    }

}
