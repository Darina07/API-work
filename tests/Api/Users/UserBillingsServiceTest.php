<?php

namespace Tests\Api\Users;

use DateTime;
use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Users\UserBillingsService;
use Tests\SuppTest;

class UserBillingsServiceTest extends SuppTest
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
        $service = $container->get(UserBillingsService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);
        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        unset($responseObj['active']);
        unset($expectedObj['active']);
        $this->assertSame($expectedObj, $responseObj);
    }

    public function providerTestInvoke()
    {
        return[
            $this->validAdminRequest(),
            $this->validAdminNoActiveBillingRequest(),
            $this->validSuperAdminRequest()
        ];
    }

    private function validAdminRequest()
    {
        $serverParams = [];
        $serverParams["SERVER_NAME"] = "dev.support.hph.io";
        $serverParams["SERVER_ADDR"] = "130.185.207.213";
        $serverParams["REMOTE_ADDR"] = "130.185.207.200";
        $serverParams["REQUEST_SCHEME"] = "https";

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')->willReturn($serverParams);
        $routeArgs = ["id" => 1];

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 1;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-03-28 01:00:00'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validAdminUserBillingsResponse.json";
        $this->assertFileExists($jsonResponsePath);
        $jsonResponse = file_get_contents($jsonResponsePath);
        $resStream = fopen('php://memory', 'r+');
        fwrite($resStream, $jsonResponse);
        rewind($resStream);
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $expectedResponse->method('getBody')->willReturn(new Stream($resStream));

        return [$request, $routeArgs, $container, $expectedResponse];
    }

    private function validAdminNoActiveBillingRequest()
    {
        $serverParams = [];
        $serverParams["SERVER_NAME"] = "dev.support.hph.io";
        $serverParams["SERVER_ADDR"] = "130.185.207.213";
        $serverParams["REMOTE_ADDR"] = "130.185.207.200";
        $serverParams["REQUEST_SCHEME"] = "https";

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')->willReturn($serverParams);
        $routeArgs = ["id" => 4];

        $user = $this->createMock(User::class);
        $user->id = '4';
        $user->role = 1;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-03-28 01:00:00'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validAdminUserBillingsNoActiveResponse.json";
        $this->assertFileExists($jsonResponsePath);
        $jsonResponse = file_get_contents($jsonResponsePath);
        $resStream = fopen('php://memory', 'r+');
        fwrite($resStream, $jsonResponse);
        rewind($resStream);
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $expectedResponse->method('getBody')->willReturn(new Stream($resStream));

        return [$request, $routeArgs, $container, $expectedResponse];
    }


    private function validSuperAdminRequest()
    {
        $serverParams = [];
        $serverParams["SERVER_NAME"] = "dev.support.hph.io";
        $serverParams["SERVER_ADDR"] = "130.185.207.213";
        $serverParams["REMOTE_ADDR"] = "130.185.207.200";
        $serverParams["REQUEST_SCHEME"] = "https";
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')->willReturn($serverParams);
        $routeArgs = ["id" => 1];

        $user = $this->createMock(User::class);
        $user->id = '3';
        $user->role = 2;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDejaVu((new DateTime('2023-03-28 01:00:00'))->getTimestamp())
            ->withDatabase()
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validSuperAdminUserBillingsResponse.json";
        $this->assertFileExists($jsonResponsePath);
        $jsonResponse = file_get_contents($jsonResponsePath);
        $resStream = fopen('php://memory', 'r+');
        fwrite($resStream, $jsonResponse);
        rewind($resStream);
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $expectedResponse->method('getBody')->willReturn(new Stream($resStream));

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

        $service = $container->get(UserBillingsService::class);
        $response = $service->__invoke($request, $routeArgs);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        $this->assertSame($expectedObj, $responseObj);

    }

    public function providerTestInvokeFailures()
    {
        return [
            $this->nonAdminInternal()
        ];
    }

    private function nonAdminInternal()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ["id" => 1];

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 3;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-03-28'))->getTimestamp())
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

