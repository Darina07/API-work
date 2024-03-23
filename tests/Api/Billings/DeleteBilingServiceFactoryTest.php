<?php

namespace Tests\Api\Billings;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Billings\AdminDeleteBilling;
use Supp\Api\Billings\DeleteBilingServiceFactory;
use Supp\Api\Billings\OtherRolesDeleteBilling;
use Tests\SuppTest;

class DeleteBilingServiceFactoryTest extends SuppTest
{
    /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestDeleteBillingServiceFactory
     * @return void
     */
    public function testDeleteBillingServiceFactory(Container $container, $expectedClass): void
    {
        $service = DeleteBilingServiceFactory::getService($container);
        $this->assertInstanceOf($expectedClass, $service);
    }

    public function providerTestDeleteBillingServiceFactory()
    {
        return [
            $this->validRequest(1),
            $this->validRequest(2),
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
        return [$container, AdminDeleteBilling::class];
    }

    private function nonValidRequest(int $roleId)
    {
        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->role = $roleId;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->getContainer();

        /* </container with user> */

        return [$container, OtherRolesDeleteBilling::class];
    }
}
