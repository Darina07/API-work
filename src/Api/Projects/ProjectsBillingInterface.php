<?php

namespace Supp\Api\Projects;

use Psr\Http\Message\ServerRequestInterface;

interface ProjectsBillingInterface
{
    public function getProjectsBilling(ServerRequestInterface $request, array $routeArgs = []);
}