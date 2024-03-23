<?php

namespace Tests\Api\Users;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Users\GetUsersAdmins;
use Supp\Api\Users\GetUsersOtherRoles;
use Supp\Api\Users\GetUsersServiceFactory;
use Tests\SuppTest;

class GetUsersServiceFactoryTest extends SuppTest
{

    /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestGetUsersServiceFactory
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetUsers(Container $container,$expectedClass): void
    {
        $this->assertInstanceOf($expectedClass, GetUsersServiceFactory::getUsers($container));
    }

    public function providerTestGetUsersServiceFactory()
    {
        return[
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

        return [$container, GetUsersAdmins::class];
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

        return [$container, GetUsersOtherRoles::class];
    }
}
