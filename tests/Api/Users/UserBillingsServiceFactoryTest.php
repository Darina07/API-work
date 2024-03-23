<?php

namespace Tests\Api\Users;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Users\AdminBillings;
use Supp\Api\Users\OtherRolesBillings;
use Supp\Api\Users\SuperAdminBillings;
use Supp\Api\Users\UserBillingsServiceFactory;
use Tests\SuppTest;

class UserBillingsServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestUserBillingsServiceFactory
     */
    public function testGetService(Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, UserBillingsServiceFactory::getBillings($container));
    }

    public function providerTestUserBillingsServiceFactory()
    {
        return [
            $this->addSupportedAdminRequest(1),
            $this->addSupportedSuperAdminRequest(2),
            $this->addSupportedClientRequest(3)
        ];

    }

    private function addSupportedAdminRequest(int $role) :array
    {
        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $role;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [ $container,  AdminBillings::class ];
    }

    private function addSupportedSuperAdminRequest(int $role) :array
    {
        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $role;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [ $container,  SuperAdminBillings::class ];
    }

    private function addSupportedClientRequest(int $role) :array
    {
        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $role;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [ $container,  OtherRolesBillings::class ];
    }
}
