<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\GetTicketsByStatusServiceFactory;
use Supp\Api\Tickets\OtherRolesTicketsByStatus;
use Supp\Api\Tickets\SuperAdminTicketsByStatus;
use Tests\SuppTest;

class GetTicketsByStatusServiceFactoryTest extends SuppTest
{
  /**
   * @param Container $container
   * @param $expectedClass
   * @dataProvider providerTestGetTicketsByStatusServiceFactory
   * @return void
   */
  public function testStatusServiceFactory(Container $container, $expectedClass): void
  {
    $this->assertInstanceOf($expectedClass, GetTicketsByStatusServiceFactory::getService($container));
  }

  public function providerTestGetTicketsByStatusServiceFactory()
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

    return [$container, SuperAdminTicketsByStatus::class];
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

    return [$container, OtherRolesTicketsByStatus::class];
  }
}
