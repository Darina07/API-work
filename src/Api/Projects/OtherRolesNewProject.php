<?php

namespace Supp\Api\Projects;

use Exception;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OtherRolesNewProject implements NewProjectInterface
{

    protected ?Container $container = null;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    /**
     * @throws Exception
     */
    public function saveNewProject(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        throw new Exception("Permission denied", 403);
    }

}