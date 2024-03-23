<?php

namespace Tests\Api\Billings;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Billings\AdminNewBilling;
use Supp\Api\Billings\NewBillingServiceFactory;
use PHPUnit\Framework\TestCase;
use Supp\Api\Billings\OtherRolesNewBilling;
use Tests\SuppTest;

class NewBillingServiceFactoryTest extends SuppTest
{
    /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestNewBillingServiceFactory
     * @return void
     */
    public function testNewBillingServiceFactory(Container $container, $expectedClass): void
    {
        $service = NewBillingServiceFactory::getService($container);
        $this->assertInstanceOf($expectedClass, $service);
    }

    public function providerTestNewBillingServiceFactory()
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

        return [$container, AdminNewBilling::class];
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

        return [$container, OtherRolesNewBilling::class];
    }
}
