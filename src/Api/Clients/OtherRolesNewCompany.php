<?php

namespace Supp\Api\Clients;

use Exception;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OtherRolesNewCompany implements NewCompanyInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    /**
     * @throws Exception
     */
    public function saveNewCompany(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        throw new Exception("Permission denied", 403);
    }

}