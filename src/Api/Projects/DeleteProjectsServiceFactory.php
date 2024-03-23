<?php

namespace Supp\Api\Projects;

use League\Container\Container;

class DeleteProjectsServiceFactory
{
    public static function getService(Container $container)
    {
        switch ($container->get('current_user')->role) {
            case 2:
                return $container->get(SuperAdminDeleteProject::class);
            default:
                return $container->get(OtherRolesDeleteProject::class);
        }
    }
}