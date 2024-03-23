<?php

namespace Supp\Api\Clients;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class ClientService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = ClientServiceFactory::getService($this->container);
            $data = $service->getAllClients($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }
        return new JsonResponse($data, 200);
    }
}