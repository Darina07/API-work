<?php

namespace Supp\Api\Projects;

use Psr\Http\Message\ServerRequestInterface;

interface NewProjectInterface
{
    public function saveNewProject(ServerRequestInterface $request, array $routeArgs = []);
}