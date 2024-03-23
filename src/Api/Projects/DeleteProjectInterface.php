<?php

namespace Supp\Api\Projects;

use Psr\Http\Message\ServerRequestInterface;

interface DeleteProjectInterface
{
 public function deleteProject(ServerRequestInterface $request, array $routeArgs = []);
}