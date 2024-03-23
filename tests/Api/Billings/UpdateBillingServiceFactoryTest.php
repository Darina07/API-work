<?php

namespace Tests\Api\Billings;

use Hphio\Auth\Models\User;
use League\Container\Container;
use Supp\Api\Billings\AdminUpdateBilling;
use Supp\Api\Billings\OtherRolesUpdateBilling;
use Supp\Api\Billings\UpdateBillingServiceFactory;
use PHPUnit\Framework\TestCase;
use Tests\SuppTest;

class UpdateBillingServiceFactoryTest extends SuppTest
{
    /**
     * @param Container $container
     * @param $expectedClass
     * @dataProvider providerTestUpdateBillingServiceFactory
     * @return void
     */
    public function testUpdateBillingServiceFactory(Container $container, $expectedClass): void
    {
        $service = UpdateBillingServiceFactory::getService($container);
        $this->assertInstanceOf($expectedClass, $service);
    }

    public function providerTestUpdateBillingServiceFactory()
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

        return [$container, AdminUpdateBilling::class];
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

        return [$container, OtherRolesUpdateBilling::class];
    }
}
