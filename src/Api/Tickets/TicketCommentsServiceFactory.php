<?php

namespace Supp\Api\Tickets;

use League\Container\Container;

class TicketCommentsServiceFactory
{
    public static function getTicketComments(Container $container) {
        switch($container->get('current_user')->role) {
            case 1: //Admin
            case 2: // Super Admin
                return $container->get(AdminTicketComments::class);
            default:
                return $container->get(OtherRolesTicketComments::class);

        }
    }
}