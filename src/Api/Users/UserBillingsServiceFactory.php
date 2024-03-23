<?php

namespace Supp\Api\Users;

use League\Container\Container;

class UserBillingsServiceFactory
{
    /**
     * @param \League\Container\Container $container
     * @return array|mixed|object|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getBillings(Container $container)
    {
        switch ($container->get('current_user')->role) {
            case 1: //Admin
                return $container->get(AdminBillings::class);
            case 2: //Super Admin
                return $container->get(SuperAdminBillings::class);
            default: // Other Roles
                return $container->get(OtherRolesBillings::class);
        }
    }
}
