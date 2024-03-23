<?php

namespace Supp\Api\Tickets;

use Psr\Http\Message\ServerRequestInterface;

interface TicketsByStatusInterface
{
    public function getTicketsByStatus(ServerRequestInterface $request, array $routeArgs = []);
}