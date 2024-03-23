<?php

namespace Supp\Api\Users;

use Psr\Http\Message\ServerRequestInterface;

interface UserBillingsInterface
{
    public function getBillings(ServerRequestInterface $request, array $routeArgs = []): array;
}
