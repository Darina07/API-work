<?php

namespace Tests\Api\Billings;

use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Billings\DeleteBillingService;
use Tests\SuppTest;

class DeleteBillingServiceTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestInvoke
     */
    public function test__invoke(ServerRequestInterface $request,
                                 array                  $routeArgs,
                                 Container              $container,
                                 ResponseInterface      $expectedResponse)
    {
        $this->loadDataSet();
        $service = $container->get(DeleteBillingService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(EmptyResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);
        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        $this->assertSame($expectedObj, $responseObj);
    }

    public function providerTestInvoke()
    {
        return[
            $this->validAdminRequest(1),
            $this->validAdminRequest(2)
        ];
    }

    private function validAdminRequest($roleId)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 1];

        $user = $this->createMock(User::class);
        $user->id = 1;
        $user->role = $roleId;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->getContainer();

        $expectedResponse = $this->createMock(EmptyResponse::class);
        $expectedResponse->method('getStatusCode')->willReturn(204);

        return [$request, $routeArgs, $container, $expectedResponse];
    }

    /**
     * @return void
     * @dataProvider providerTestInvokeFailures
     */
    public function test__invokeFailures(ServerRequestInterface $request,
                                         array                  $routeArgs,
                                         Container              $container,
                                         ResponseInterface      $expectedResponse) {

        $service = $container->get(DeleteBillingService::class);
        $response = $service->__invoke($request, $routeArgs);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));
        $this->assertSame($expectedObj, $responseObj);
    }

    public function providerTestInvokeFailures()
    {
        return [
            $this->nonAdminRequest(),
            $this->nonCurrentUserBilling(),
            $this->adminNonCurrentUserBilling()
        ];
    }

    private function nonAdminRequest()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id'=>5];

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = 3;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/noPermission.json";
        $this->assertFileExists($jsonResponsePath);
        $jsonResponse = file_get_contents($jsonResponsePath);
        $resStream = fopen('php://memory', 'r+');
        fwrite($resStream, $jsonResponse);
        rewind($resStream);
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(403);
        $expectedResponse->method('getBody')->willReturn(new Stream($resStream));

        return [$request, $routeArgs, $container, $expectedResponse];
    }

  private function nonCurrentUserBilling ()
  {
    $request = $this->createMock(ServerRequestInterface::class);
    $routeArgs = ['id'=>6];

    $user = $this->createMock(User::class);
    $user->id = 2;
    $user->role = 3;

    $container = $this->buildContainer()
        ->withCurrentUser($user)
        ->withConfig()
        ->withDatabase()
        ->getContainer();

    $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/noPermission.json";
    $this->assertFileExists($jsonResponsePath);
    $jsonResponse = file_get_contents($jsonResponsePath);
    $resStream = fopen('php://memory', 'r+');
    fwrite($resStream, $jsonResponse);
    rewind($resStream);
    $expectedResponse = $this->createMock(ResponseInterface::class);
    $expectedResponse->method('getStatusCode')->willReturn(403);
    $expectedResponse->method('getBody')->willReturn(new Stream($resStream));

    return [$request, $routeArgs, $container, $expectedResponse];
  }

  private function adminNonCurrentUserBilling ()
  {
    $request = $this->createMock(ServerRequestInterface::class);
    $routeArgs = ['id'=>8];

    $user = $this->createMock(User::class);
    $user->id = 1;
    $user->role = 1;

    $container = $this->buildContainer()
        ->withCurrentUser($user)
        ->withConfig()
        ->withDatabase()
        ->getContainer();

    $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/noPermission.json";
    $this->assertFileExists($jsonResponsePath);
    $jsonResponse = file_get_contents($jsonResponsePath);
    $resStream = fopen('php://memory', 'r+');
    fwrite($resStream, $jsonResponse);
    rewind($resStream);
    $expectedResponse = $this->createMock(ResponseInterface::class);
    $expectedResponse->method('getStatusCode')->willReturn(403);
    $expectedResponse->method('getBody')->willReturn(new Stream($resStream));

    return [$request, $routeArgs, $container, $expectedResponse];
  }


}
