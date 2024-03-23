<?php

namespace Supp\Api\Tickets;

use League\Container\Container;

class AssignTicketServiceFactory
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
                return $container->get(SuperAdminAssignTicket::class);
            default: // Other Roles
                return $container->get(OtherRolesAssignTicket::class);
        }
    }
}