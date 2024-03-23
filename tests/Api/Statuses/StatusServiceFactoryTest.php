<?php

namespace Tests\Api\Statuses;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Statuses\OtherRolesStatuses;
use Supp\Api\Statuses\StatusServiceFactory;
use Supp\Api\Statuses\SuperAdminStatuses;
use Tests\SuppTest;

class StatusServiceFactoryTest extends SuppTest
{
  /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestStatusServiceFactory
     * @return void
     */
 public function testStatusServiceFactory(Container $container, $expectedClass): void
    {
        $this->assertInstanceOf($expectedClass, StatusServiceFactory::getService($container));
    }

    public function providerTestStatusServiceFactory()
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

        return [$container, SuperAdminStatuses::class];
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

        return [$container, OtherRolesStatuses::class];
    }
}

