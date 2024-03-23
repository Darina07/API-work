<?php

namespace Supp\Api\Clients;

use Psr\Http\Message\ServerRequestInterface;

interface ClientServiceInterface
{
    public function getAllClients(ServerRequestInterface $request, array $routeArgs = []);
}