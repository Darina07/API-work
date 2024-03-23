<?php

namespace Tests\Api\Projects;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Projects\GetProjectsServiceFactory;
use Supp\Api\Projects\OtherRolesProjets;
use Supp\Api\Projects\SuperAdminProjects;
use Tests\SuppTest;

class GetProjectsServiceFactoryTest extends SuppTest
{
    /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestGetProjectsServiceFactory
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetProjectsServiceFactory(Container $container, $expectedClass): void
    {
        $this->assertInstanceOf($expectedClass, GetProjectsServiceFactory::getService($container));
    }

    public function providerTestGetProjectsServiceFactory()
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

        return [$container, SuperAdminProjects::class];
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

        return [$container, OtherRolesProjets::class];
    }
}
