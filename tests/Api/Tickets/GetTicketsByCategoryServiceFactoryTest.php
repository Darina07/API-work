<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\GetTicketsByCategoryServiceFactory;
use Supp\Api\Tickets\OtherRolesTicketsByCategory;
use Supp\Api\Tickets\SuperAdminTicketsByCategory;
use Tests\SuppTest;

class GetTicketsByCategoryServiceFactoryTest extends SuppTest
{
  /**
   * @param Container $container
   * @param $expectedClass
   * @dataProvider providerTestGetTicketsByCategoryServiceFactory
   * @return void
   */
  public function testStatusServiceFactory(Container $container, $expectedClass): void
  {
    $this->assertInstanceOf($expectedClass, GetTicketsByCategoryServiceFactory::getService($container));
  }

  public function providerTestGetTicketsByCategoryServiceFactory()
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

    return [$container, SuperAdminTicketsByCategory::class];
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

    return [$container, OtherRolesTicketsByCategory::class];
  }
}
