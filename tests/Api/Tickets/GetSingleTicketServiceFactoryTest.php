<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\AdminSingleTicket;
use Supp\Api\Tickets\CurrentUserSingleTicket;
use Supp\Api\Tickets\GetSingleTicketServiceFactory;
use Supp\Api\Tickets\OtherRolesSingleTicket;
use Tests\SuppTest;

class GetSingleTicketServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestGetSingleTicketServiceFactory
     */
    public function testGetService(array $routeArgs, Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, GetSingleTicketServiceFactory::getService($container, $routeArgs['id']));
    }

    public function providerTestGetSingleTicketServiceFactory()
    {
        return [
            $this->addSupportedAdminRequest(1),
            $this->addSupportedAdminRequest(2),
            $this->addSupportedClientRequest(3),
            $this->addSupportedOwnerRequest(3)
        ];

    }

    private function addSupportedAdminRequest(int $role) :array
    {
        /* <container with user> */
        $routeArgs = ["id" => 1];
        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $role;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withDatabase()
            ->getContainer();

        /* </container with user> */

        return [$routeArgs, $container,  AdminSingleTicket::class ];
    }

    private function addSupportedClientRequest(int $role) :array
    {
        /* <container with user> */
        $routeArgs = ["id" => 2];

        $user = $this->createMock(User::class);
        $user->id = 5;
        $user->role = $role;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withDatabase()
            ->getContainer();

        /* </container with user> */

        return [$routeArgs ,$container,  OtherRolesSingleTicket::class ];
    }

    private function addSupportedOwnerRequest($role): array
    {
        /* <container with user> */
        $routeArgs = ["id" => 1];

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $role;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withDatabase()
            ->getContainer();

        /* </container with user> */

        return [$routeArgs ,$container,  CurrentUserSingleTicket::class ];
    }
}
