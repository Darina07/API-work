<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\AdminTicketComments;
use Supp\Api\Tickets\OtherRolesTicketComments;
use Supp\Api\Tickets\TicketCommentsServiceFactory;
use Tests\SuppTest;

class TicketCommentsServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestTicketCommentsServiceFactory
     */
    public function testGetService(Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, TicketCommentsServiceFactory::getTicketComments($container));
    }

    public function providerTestTicketCommentsServiceFactory()
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

        return [ $container,  AdminTicketComments::class ];
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

        return [ $container,  OtherRolesTicketComments::class ];
    }
}
