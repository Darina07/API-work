<?php

namespace Supp\Api\Projects;

use Exception;
use League\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class OtherRolesProjets implements ProjectInterface
{

    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    /**
     * @throws Exception
     */
    public function getProjects(ServerRequestInterface $request, array $routeArgs = []): array
    {
        throw new Exception("Permission denied", 403);
    }

}