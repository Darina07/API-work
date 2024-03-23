<?php

namespace Supp\Api\Projects;

use League\Container\Container;

class NewProjectServiceFactory
{
    public static function getService(Container $container) {
        switch($container->get('current_user')->role) {
            case 1:
            case 2:
                return $container->get(AdminNewProject::class);
            default:
                return $container->get(OtherRolesNewProject::class);
        }
    }
}