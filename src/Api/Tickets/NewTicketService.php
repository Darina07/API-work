<?php

namespace Supp\Api\Tickets;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class NewTicketService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = NewTicketServiceFactory::getNewTicket($this->container, $routeArgs['filter']);
            return $service->__invoke($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }
    }
}