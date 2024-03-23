<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\OtherRolesTicketsReport;
use Supp\Api\Tickets\SuperAdminTicketsReport;
use Supp\Api\Tickets\TicketsReportServiceFactory;
use Tests\SuppTest;

class TicketsReportServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestTicketsReportServiceFactory
     */
    public function testGetService(Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, TicketsReportServiceFactory::getService($container));
    }

    public function providerTestTicketsReportServiceFactory()
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

        return [ $container,  SuperAdminTicketsReport::class ];
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

        return [ $container,  OtherRolesTicketsReport::class ];
    }
}
