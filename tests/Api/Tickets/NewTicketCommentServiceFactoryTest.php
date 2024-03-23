<?php

namespace Tests\Api\Tickets;


use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Tickets\AdminPostTicketComment;
use Supp\Api\Tickets\NewTicketCommentServiceFactory;
use Supp\Api\Tickets\OtherRolesPostTicketComment;
use Tests\SuppTest;

class NewTicketCommentServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestGetService
     */
    public function testGetService(Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, NewTicketCommentServiceFactory::getNewTicketComment($container));
    }

    public function providerTestGetService()
    {
        return [
            $this->addSupportedAdminRequest(1),
            $this->addSupportedClientRequest(3)
        ];

    }

    private function addSupportedAdminRequest(int $role) :array
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

        return [ $container,  AdminPostTicketComment::class ];
    }

    private function addSupportedClientRequest(int $role) :array
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

        return [ $container,  OtherRolesPostTicketComment::class ];
    }


}




