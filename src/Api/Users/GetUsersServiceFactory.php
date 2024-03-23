<?php

namespace Supp\Api\Users;

use League\Container\Container;

class GetUsersServiceFactory
{
    /**
     * @param \League\Container\Container $container
     * @return array|mixed|object|void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getUsers(Container $container)
    {
        switch ($container->get('current_user')->role) {
            case 1: //Admin
            case 2: //Super Admin
                return $container->get(GetUsersAdmins::class);
            default: // Other Roles
                return $container->get(GetUsersOtherRoles::class);
        }
    }
}