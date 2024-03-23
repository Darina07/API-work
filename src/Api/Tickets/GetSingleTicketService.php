<?php

namespace Supp\Api\Tickets;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class GetSingleTicketService extends SuppApiService
{
    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = GetSingleTicketServiceFactory::getService($this->container, $routeArgs['id']);
            return $service->__invoke($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }
    }
}