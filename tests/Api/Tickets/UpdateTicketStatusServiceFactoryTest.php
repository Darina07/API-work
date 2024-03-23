<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\OtherRolesUpdateTicketStatus;
use Supp\Api\Tickets\SuperAdminUpdateTicketStatus;
use Supp\Api\Tickets\UpdateTicketStatusServiceFactory;
use Tests\SuppTest;

class UpdateTicketStatusServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestUpdateTicketStatusServiceFactory
     */
    public function testGetService(Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, UpdateTicketStatusServiceFactory::getService($container));
    }

    public function providerTestUpdateTicketStatusServiceFactory()
    {
        return [
            $this->addSupportedAdminRequest(1),
            $this->addSupportedAdminRequest(2),
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

        return [ $container,  SuperAdminUpdateTicketStatus::class ];
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

        return [ $container,  OtherRolesUpdateTicketStatus::class ];
    }
}
