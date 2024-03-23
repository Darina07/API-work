<?php

namespace Supp\Api\Tickets;

use Exception;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\SuppApiService;

class AssignTicketService extends SuppApiService
{
    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = AssignTicketServiceFactory::getService($this->container);
            $service->assignTicket($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }

        return new EmptyResponse(201);
    }

}
