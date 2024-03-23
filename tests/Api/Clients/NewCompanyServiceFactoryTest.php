<?php

namespace Tests\Api\Clients;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Clients\AdminNewCompany;
use Supp\Api\Clients\NewCompanyServiceFactory;
use Supp\Api\Clients\OtherRolesNewCompany;
use Tests\SuppTest;

class NewCompanyServiceFactoryTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestGetService
     */
    public function testGetService(Container $container, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, NewCompanyServiceFactory::getService($container));
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

        return [ $container,  AdminNewCompany::class ];
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

        return [ $container,  OtherRolesNewCompany::class ];
    }


}





