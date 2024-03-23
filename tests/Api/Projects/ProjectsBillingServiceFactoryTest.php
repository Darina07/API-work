<?php

namespace Tests\Api\Projects;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Projects\OtherRolesProjetsBilling;
use Supp\Api\Projects\ProjectsBillingServiceFactory;
use Supp\Api\Projects\SuperAdminProjectsBilling;
use Tests\SuppTest;

class ProjectsBillingServiceFactoryTest extends SuppTest
{
    /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestProjectsBillingServiceFactory
     * @return void
     */
 public function testProjectsBillingServiceFactory(Container $container, $expectedClass): void
    {
        $this->assertInstanceOf($expectedClass, ProjectsBillingServiceFactory::getService($container));
    }

    public function providerTestProjectsBillingServiceFactory()
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

        return [$container, SuperAdminProjectsBilling::class];
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

        return [$container, OtherRolesProjetsBilling::class];
    }
}
