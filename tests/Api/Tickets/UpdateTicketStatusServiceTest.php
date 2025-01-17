<?php

namespace Tests\Api\Tickets;

use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Tickets\UpdateTicketStatusService;
use Tests\SuppTest;

class UpdateTicketStatusServiceTest extends SuppTest
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
        $service = $container->get(UpdateTicketStatusService::class);
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
        $routeArgs = ['id' => 2];
        /* <container with user> */
        $jsonPayload= '{"status": 2, "comment_id": 4}';
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = $roleId;

        $container = $this->buildContainer()
            ->withConfig()
            ->withDatabase()
            ->withCurrentUser($user)
            ->getContainer();

        $expectedResponse = $this->createMock(EmptyResponse::class);
        $expectedResponse->method('getStatusCode')->willReturn(204);
        /* </expected response template> */

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

        $service = $container->get(UpdateTicketStatusService::class);
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
            $this->ticketWithoutComment()
        ];
    }

    private function nonAdminInternal()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 2];
        /* <container with user> */
        $jsonPayload= '{"status": 2, "comment_id": 4}';
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = 3;

        $container = $this->buildContainer()
            ->withConfig()
            ->withDatabase()
            ->withCurrentUser($user)
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/noPermissions.json";
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

    private function ticketWithoutComment()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 1];
        /* <container with user> */
        $jsonPayload= '{"status": 2}';
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = 1;

        $container = $this->buildContainer()
            ->withConfig()
            ->withDatabase()
            ->withCurrentUser($user)
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/ticketWithoutComment.json";
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

