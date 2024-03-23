<?php

namespace Supp\Api\Projects;

use Exception;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class DeleteProjectsService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = DeleteProjectsServiceFactory::getService($this->container);
            $service->deleteProject($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }
        return new EmptyResponse(204);
    }
}