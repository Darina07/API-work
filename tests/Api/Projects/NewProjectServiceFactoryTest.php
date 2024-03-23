<?php

namespace Tests\Api\Projects;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Projects\AdminNewProject;
use Supp\Api\Projects\NewProjectServiceFactory;
use Supp\Api\Projects\OtherRolesNewProject;
use Tests\SuppTest;

class NewProjectServiceFactoryTest extends SuppTest
{
    /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestNewProjectServiceFactory
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testNewProjectServiceFactory(Container $container, $expectedClass): void
    {
        $this->assertInstanceOf($expectedClass, NewProjectServiceFactory::getService($container));
    }

    public function providerTestNewProjectServiceFactory()
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

        return [$container, AdminNewProject::class];
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

        return [$container, OtherRolesNewProject::class];
    }
}
