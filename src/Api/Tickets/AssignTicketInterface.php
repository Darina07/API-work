<?php

namespace Supp\Api\Tickets;

use Psr\Http\Message\ServerRequestInterface;

interface AssignTicketInterface
{
    public function assignTicket(ServerRequestInterface $request, array $routeArgs = []);
}