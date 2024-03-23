<?php

namespace Supp\Api\Projects;

use Psr\Http\Message\ServerRequestInterface;

interface ProjectInterface
{
    public function getProjects(ServerRequestInterface $request, array $routeArgs = []);
}