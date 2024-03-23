<?php

namespace Supp\Api\Tickets;

use Psr\Http\Message\ServerRequestInterface;

interface TicketsByCategoryInterface
{
    public function getTicketsByCategory(ServerRequestInterface $request, array $routeArgs = []);
}