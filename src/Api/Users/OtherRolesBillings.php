<?php

namespace Supp\Api\Users;

use Exception;
use League\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class OtherRolesBillings implements UserBillingsInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    /**
     * @throws Exception
     */
    public function getBillings(ServerRequestInterface $request, array $routeArgs = []): array
    {
        throw new Exception("You do not have permission to do that.", 403);
    }
}
