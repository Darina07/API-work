<?php

namespace Tests\Api\Billings;

use DateTime;
use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Billings\UpdateBillingService;
use Tests\SuppTest;

class UpdateBillingServiceTest extends SuppTest
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
        $service = $container->get(UpdateBillingService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);
        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));
        $this->assertSame($expectedObj, $responseObj);
    }

    public function providerTestInvoke()
    {
        return[
            $this->validAdminRequest(1),
            $this->validAdminRequest(2),
            $this->validAdminUserInputTimerRequest(1)
        ];
    }

    private function validAdminRequest($roleId)
    {
        $serverParams = [];
        $serverParams["SERVER_NAME"] = "dev.support.hph.io";
        $serverParams["SERVER_ADDR"] = "130.185.207.213";
        $serverParams["REMOTE_ADDR"] = "130.185.207.200";
        $serverParams["REQUEST_SCHEME"] = "https";

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')->willReturn($serverParams);
        $routeArgs = ['id' => 1];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validAdminUpdateBillingRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $roleId;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-03-20 07:35:00'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validAdminUpdateBillingResponse.json";
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

    private function validAdminUserInputTimerRequest(int $roleId)
    {
        $serverParams = [];
        $serverParams["SERVER_NAME"] = "dev.support.hph.io";
        $serverParams["SERVER_ADDR"] = "130.185.207.213";
        $serverParams["REMOTE_ADDR"] = "130.185.207.200";
        $serverParams["REQUEST_SCHEME"] = "https";

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getServerParams')->willReturn($serverParams);
        $routeArgs = ['id' => 1];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validAdminUserInputTimerUpdateBillingRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $roleId;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-03-20 08:40:00'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validAdminUserInputTimerUpdateBillingResponse.json";
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

        $service = $container->get(UpdateBillingService::class);
        $response = $service->__invoke($request, $routeArgs);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        $this->assertSame($expectedObj, $responseObj);

    }

    public function providerTestInvokeFailures()
    {
        return [
            $this->nonAdminInternal(),
            $this->notSelectedProject()
        ];
    }

    private function nonAdminInternal()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id'=>1];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validAdminUpdateBillingRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        $user = $this->createMock(User::class);
        $user->id = '1';
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

    private function notSelectedProject()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 1];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/notSelectedProjectAdminUpdateBillingRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 1;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-03-20'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/notSelectedProjectAdminUpdateBillingResponse.json";
        $this->assertFileExists($jsonResponsePath);
        $jsonResponse = file_get_contents($jsonResponsePath);
        $resStream = fopen('php://memory', 'r+');
        fwrite($resStream, $jsonResponse);
        rewind($resStream);
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(400);
        $expectedResponse->method('getBody')->willReturn(new Stream($resStream));

        return [$request, $routeArgs, $container, $expectedResponse];
    }

}


