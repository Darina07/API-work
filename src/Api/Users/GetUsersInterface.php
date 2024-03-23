<?php

namespace Supp\Api\Users;

use Psr\Http\Message\ServerRequestInterface;

interface GetUsersInterface
{
    public function getUsers(ServerRequestInterface $request, array $routeArgs = []): array;
}