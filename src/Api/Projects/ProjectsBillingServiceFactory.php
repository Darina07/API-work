<?php

namespace Supp\Api\Projects;

use League\Container\Container;

class ProjectsBillingServiceFactory
{
    /**
     * @param \League\Container\Container $container
     * @return array|mixed|object|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getService(Container $container)
    {
        switch ($container->get('current_user')->role) {
            case 1: //Admin
            case 2: //Super Admin
                return $container->get(SuperAdminProjectsBilling::class);
            default: // Other Roles
                return $container->get(OtherRolesProjetsBilling::class);
        }
    }
}