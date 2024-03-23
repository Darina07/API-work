<?php

namespace Tests\Api\Projects;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Projects\DeleteProjectsServiceFactory;
use Supp\Api\Projects\OtherRolesDeleteProject;
use Supp\Api\Projects\SuperAdminDeleteProject;
use Tests\SuppTest;

class DeleteProjectsServiceFactoryTest extends SuppTest
{
       /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestDeleteProjectsServiceFactory
     * @return void
     */
    public function testDeleteProjectsServiceFactory(Container $container, $expectedClass): void
    {
        $service = DeleteProjectsServiceFactory::getService($container);
        $this->assertInstanceOf($expectedClass, $service);
    }

    public function providerTestDeleteProjectsServiceFactory(): array
    {
        return [
            $this->validRequest(2),
            $this->nonValidRequest(1),
            $this->nonValidRequest(3)
        ];
    }

    private function validRequest(int $roleId)
    {
        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->role = $roleId;


        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */
        return [$container, SuperAdminDeleteProject::class];
    }

    private function nonValidRequest(int $roleId): array
    {
        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->role = $roleId;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [$container, OtherRolesDeleteProject::class];
    }
}
