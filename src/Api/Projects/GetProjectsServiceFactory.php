<?php

namespace Supp\Api\Projects;

use League\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class GetProjectsServiceFactory
{
    /**
     * @param Container $container
     * @return array|mixed|object|void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getService(Container $container)
    {
        switch ($container->get('current_user')->role) {
            case 1: //Admin
            case 2: //Super Admin
                return $container->get(SuperAdminProjects::class);
            default: // Other Roles
                return $container->get(OtherRolesProjets::class);
        }
    }
}