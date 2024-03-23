<?php

namespace Supp\Api\Tickets;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OtherRolesSingleTicket extends NullGetSingleTicket
{
    public function __invoke(ServerRequestInterface $request, array $routeArgs): ResponseInterface
    {
        throw new Exception("Only Admins are allowed to access this service.", 403);
    }
}