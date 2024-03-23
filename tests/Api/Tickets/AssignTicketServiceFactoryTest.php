<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\AssignTicketServiceFactory;
use PHPUnit\Framework\TestCase;
use Supp\Api\Tickets\OtherRolesAssignTicket;
use Supp\Api\Tickets\SuperAdminAssignTicket;
use Tests\SuppTest;

class AssignTicketServiceFactoryTest extends SuppTest
{
/**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestAssignTicketServiceFactory
     * @return void
     */
 public function testStatusServiceFactory(Container $container, $expectedClass): void
    {
        $this->assertInstanceOf($expectedClass, AssignTicketServiceFactory::getService($container));
    }

    public function providerTestAssignTicketServiceFactory()
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

        return [$container, SuperAdminAssignTicket::class];
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

        return [$container, OtherRolesAssignTicket::class];
    }
}


