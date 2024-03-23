<?php

namespace Supp\Api\Clients;

use Psr\Http\Message\ServerRequestInterface;

interface NewCompanyInterface
{
    public function saveNewCompany(ServerRequestInterface $request, array $routeArgs = []);
}