<?php

namespace Supp\Api\Clients;

use League\Container\Container;

class NewCompanyServiceFactory
{
    public static function getService(Container $container) {
        switch($container->get('current_user')->role) {
            case 1:
            case 2:
                return $container->get(AdminNewCompany::class);
            default:
                return $container->get(OtherRolesNewCompany::class);
        }
    }
}