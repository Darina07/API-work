<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\AdminGetAllTickets;
use Supp\Api\Tickets\GetAllTicketsServiceFactory;
use Supp\Api\Tickets\OtherRolesGetAllTickets;
use Tests\SuppTest;

class GetAllTicketsServiceFactoryTest extends SuppTest
{
/**
* @param Container $container
* @param $expectedClass
* @dataProvider providerTestGetAllTicketsServiceFactory
* @return void
*/
  public function testGetAllTicketsServiceFactory(Container $container, $expectedClass): void
  {
    $this->assertInstanceOf($expectedClass, GetAllTicketsServiceFactory::getTicket($container));
  }

  public function providerTestGetAllTicketsServiceFactory()
  {
    return [
        $this->validRequest(1),
        $this->validRequest(2),
        $this->permissionDenied(3)
    ];
  }

  private function validRequest(int $roleID)
  {
    /* <container with user> */

    $user = $this->createMock(User::class);
    $user->role = $roleID;

    $container = $this->buildContainer()
        ->withCurrentUser($user)
        ->getContainer();

    /* </container with user> */

    return [$container, AdminGetAllTickets::class];
  }

  private function permissionDenied(int $roleID)
  {
    /* <container with user> */

    $user = $this->createMock(User::class);
    $user->role = $roleID;

    $container = $this->buildContainer()
        ->withCurrentUser($user)
        ->getContainer();

    /* </container with user> */

    return [$container, OtherRolesGetAllTickets::class];
  }
}
