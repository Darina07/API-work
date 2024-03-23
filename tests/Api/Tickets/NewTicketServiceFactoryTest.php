<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\BugReport;
use Supp\Api\Tickets\ChangeOrder;
use Supp\Api\Tickets\FeatureRequest;
use Supp\Api\Tickets\NewTicketServiceFactory;
use Tests\SuppTest;

class NewTicketServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestNewTicketServiceFactory
     */
    public function testGetService(array $routeArgs, Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, NewTicketServiceFactory::getNewTicket($container,$routeArgs['filter']));
    }

    public function providerTestNewTicketServiceFactory()
    {
        return [
            $this->addSupportedBugReportRequest(),
            $this->addSupportedFeatureRequestRequest(),
            $this->addSupportedChangeOrderRequest(),
        ];

    }

    private function addSupportedBugReportRequest() :array
    {
        /* <container with user> */
        $routeArgs = ["filter" => 1];
        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 1;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [$routeArgs ,$container,  BugReport::class ];
    }

    private function addSupportedFeatureRequestRequest()
    {
        /* <container with user> */
        $routeArgs = ["filter" => 2];

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 1;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [ $routeArgs ,$container,  FeatureRequest::class ];
    }

    private function addSupportedChangeOrderRequest()
    {
        /* <container with user> */
        $routeArgs = ["filter" => 3];

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 1;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [$routeArgs ,$container,  ChangeOrder::class ];
    }
}
