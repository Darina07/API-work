<?php

namespace Supp\Api\Tickets;

use League\Container\Container;

class GetAllTicketsServiceFactory
{
  /**
   * @param \League\Container\Container $container
   * @return array|mixed|object|void
   * @throws \Psr\Container\ContainerExceptionInterface
   * @throws \Psr\Container\NotFoundExceptionInterface
   */
  public static function getTicket(Container $container)
  {
    switch ($container->get('current_user')->role) {
      case 1: //Admin
          case 2: // Super Admin
        return $container->get(AdminGetAllTickets::class);
      default: // Other Roles
        return $container->get(OtherRolesGetAllTickets::class);
    }
  }

}





