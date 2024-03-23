<?php

namespace Supp\Api\Tickets;

use Psr\Http\Message\ServerRequestInterface;

interface TicketsReportInterface
{
    public function getTicketsReport(ServerRequestInterface $request, array $routeArgs = []);
}