<?php

namespace Tests\Api\Tickets;

use Exception;
use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Tickets\GetSingleTicketServiceFactory;
use Tests\SuppTest;

class GetSingleTicketServiceTest extends SuppTest
{
    /**
     * @return void
     * @dataProvider providerTestInvoke
     */

    public function test__invoke($request, $routeArgs, Container $container, $expectedResponse)
    {
        $this->loadDataSet();

        $service = GetSingleTicketServiceFactory::getService($container, $routeArgs['id']);
        $response = $service->__invoke($request, $routeArgs);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);
        foreach($responseObj['uploads'] as $key => $value)
        {
            unset($responseObj['uploads'][$key]['storage_path']);
            unset($expectedObj['uploads'][$key]['storage_path']);
            unset($responseObj['uploads'][$key]['image']);
            unset($expectedObj['uploads'][$key]['image']);
        }
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));
        $this->assertSame($expectedObj, $responseObj);
    }

    public function providerTestInvoke()
    {
        return [
            /*Admin*/
            $this->AdminSingleTicketBugReportRequest(),
            $this->SuperAdminSingleTicketChangeOrderRequest(),
            $this->validOtherRoleSingleTicketFeatureRequest()
        ];
    }


    private function AdminSingleTicketBugReportRequest() : array
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 1];
        /* </mock GET request> */

        /* <container with user> */
        $container = $this->buildContainer()
            ->withDatabase()
            ->getContainer();

        $user = $this->createMock(User::class);
        $user->id = 1;
        $user->role = 1;

        $container->add('current_user', $user);

        /* <expected response template> */
        $jsonPayload = file_get_contents(__DIR__ . '/fixtures/responses/AdminSingleTicketRequestResponse.json' );
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));
        /* </expected response template> */

        /* </container with user> */

        return [$request, $routeArgs, $container, $expectedResponse];
    }

    private function SuperAdminSingleTicketChangeOrderRequest() : array
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 3];
        /* </mock GET request> */

        /* <container with user> */
        $container = $this->buildContainer()
            ->withDatabase()
            ->getContainer();

        $user = $this->createMock(User::class);
        $user->id = 1;
        $user->role = 1;

        $container->add('current_user', $user);

        /* <expected response template> */
        $jsonPayload = file_get_contents(__DIR__ . '/fixtures/responses/SuperAdminSingleTicketChangeOrderRequestResponse.json' );
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));
        /* </expected response template> */

        /* </container with user> */

        return [$request, $routeArgs, $container, $expectedResponse];
    }

    private function validOtherRoleSingleTicketFeatureRequest() : array
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 2];
        /* </mock GET request> */

        /* <container with user> */
        $container = $this->buildContainer()
            ->withDatabase()
            ->getContainer();

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = 3;

        $container->add('current_user', $user);

        /* <expected response template> */
        $jsonPayload = file_get_contents(__DIR__ . '/fixtures/responses/validOtherRoleSingleTicketResponse.json' );
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));
        /* </expected response template> */

        /* </container with user> */

        return [$request, $routeArgs, $container, $expectedResponse];
    }


    /**
     * @param $request
     * @param $routeArgs
     * @param $container
     * @param $expectedException
     * @param $expectedExceptionMessage
     * @param $expectedExceptionCode
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @dataProvider providerTestInvokeExceptions
     */
    public function testInvokeExceptions($request, $routeArgs, $container, $expectedException) {
        $this->loadDataSet();

        $this->expectException($expectedException);
        $service = GetSingleTicketServiceFactory::getService($container, $routeArgs['id']);
        $service->__invoke($request, $routeArgs);

    }

    public function providerTestInvokeExceptions()
    {
        return [
            /*All other roles */
            $this->AllOtherRoles()
        ];
    }
    private function AllOtherRoles()
    {

        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 1];
        /* </mock GET request> */

        /* <container with user> */
        $container = $this->buildContainer()
            ->withDatabase()
            ->getContainer();

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = 3;

        $container->add('current_user', $user);

        $expectedException = Exception::class;
        $expectedExceptionMessage = "Only Admins are allowed to access this service.";
        $expectedExceptionCode = 403;

        /* </container with user> */

        return [$request, $routeArgs, $container, $expectedException, $expectedExceptionMessage, $expectedExceptionCode];
    }

}

