<?php

namespace Tests\Api\Clients;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Clients\AdminClients;
use Supp\Api\Clients\ClientServiceFactory;
use Supp\Api\Clients\OtherRolesClients;
use Tests\SuppTest;

class ClientServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestGetService
     */
    public function testGetService(Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, ClientServiceFactory::getService($container));
    }

    public function providerTestGetService()
    {
        $data = [];
        $data[] = $this->addSupportedRequest(1);

        $disallowedRoles = [
            3
        ];

        foreach($disallowedRoles as $role) {
            $data[] = $this->addBadRequest($role);
        }

        return $data;
    }

    private function addSupportedRequest(int $role) :array
    {
        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $role;
        $user->parent_entity = '11';

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [ $container,  AdminClients::class ];
    }

    private function addBadRequest(int $role):array
    {
        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $role;
        $user->parent_entity = '11';

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [ $container,  OtherRolesClients::class ];
    }


}
