<?php

namespace Supp\Api\Projects;

use Exception;
use League\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class OtherRolesDeleteProject implements DeleteProjectInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * @throws Exception
     */
    public function deleteProject(ServerRequestInterface $request, array $routeArgs = [])
    {
        throw new Exception("Permission denied", 403);
    }
}