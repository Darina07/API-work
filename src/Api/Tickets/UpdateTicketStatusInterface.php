<?php

namespace Supp\Api\Tickets;

use Psr\Http\Message\ServerRequestInterface;

interface UpdateTicketStatusInterface
{
    public function updateTicketStatus(ServerRequestInterface $request, array $routeArgs = []);
}