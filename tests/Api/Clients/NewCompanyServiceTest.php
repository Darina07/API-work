<?php

namespace Tests\Api\Clients;

use DateTime;
use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Clients\NewCompanyService;
use Tests\SuppTest;

class NewCompanyServiceTest extends SuppTest
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
        $service = $container->get(NewCompanyService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);
        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode());
        $this->assertSame($expectedObj, $responseObj);
    }
    public function providerTestInvoke()
    {
        return [
            $this->validAdminNewCompanyRequest(1),
            $this->validAdminNewCompanyRequest(2)
        ];
    }
    private function validAdminNewCompanyRequest(int $roleId): array
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validAdminNewCompanyRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        /* </mock GET request> */

        /* <container with user> */
        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $roleId;
        $user->parent_entity = '11';
        /**
         * Other available, common properties:
         * $user->username = '';
         * $user->email = '';
         * $user->password = '';
         * $user->nonce = '';
         * $user->first = '';
         * $user->last = '';
         * $user->created = '';
         * $user->last_login = '';
         * $user->activation_status = '';
         * $user->activated_on = '';
         * $user->password_version = '';
         **/


        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        /* </container with user> */



        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validAdminNewCompanyRequestResponse.json";
        $this->assertFileExists($jsonResponsePath);
        $jsonResponse = file_get_contents($jsonResponsePath);
        $resStream = fopen('php://memory', 'r+');
        fwrite($resStream, $jsonResponse);
        rewind($resStream);
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(201);
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

        $service = $container->get(NewCompanyService::class);
        $response = $service->__invoke($request, $routeArgs);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        $this->assertSame($expectedObj, $responseObj);

    }

    public function providerTestInvokeFailures()
    {
        return [
            $this->permissionDeniedRoles()
        ];
    }

    private function permissionDeniedRoles()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validAdminNewCompanyRequest.json");
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
}




