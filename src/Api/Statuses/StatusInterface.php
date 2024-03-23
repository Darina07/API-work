<?php

namespace Supp\Api\Statuses;

use Psr\Http\Message\ServerRequestInterface;

interface StatusInterface
{
    public function getStatuses(ServerRequestInterface $request, array $routeArgs = []);
}
