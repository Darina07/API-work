<?php

namespace Supp\Api\Projects;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\SuppApiService;

class NewProjectService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = NewProjectServiceFactory::getService($this->container);
            $data = $service->saveNewProject($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }
        return new JsonResponse($data, 201);
    }

}
