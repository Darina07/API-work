<?php

namespace Supp\Api\Statuses;

use Exception;
use League\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class OtherRolesStatuses implements StatusInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    public function getStatuses(ServerRequestInterface $request, array $routeArgs = [])
    {
        throw new Exception("Permission denied", 403);
    }
}
