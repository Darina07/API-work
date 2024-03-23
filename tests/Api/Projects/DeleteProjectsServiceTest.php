<?php

namespace Tests\Api\Projects;

use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Projects\DeleteProjectsService;
use Tests\SuppTest;

class DeleteProjectsServiceTest extends SuppTest
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
        $service = $container->get(DeleteProjectsService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(EmptyResponse::class, $response);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode());
    }

    public function providerTestInvoke()
    {
        return[
            $this->validSuperAdminRequest(2)
        ];
    }

    private function validSuperAdminRequest($roleId)
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

        $this->loadDataSet();
        $service = $container->get(DeleteProjectsService::class);
        $response = $service->__invoke($request, $routeArgs);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));
        $this->assertSame($expectedObj, $responseObj);
    }

    public function providerTestInvokeFailures(): array
    {
        return [
            $this->permissionDeniedDeleteProjectRequest(),
            $this->projectInBillings()
        ];
    }

    private function permissionDeniedDeleteProjectRequest(): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id'=>1];

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = 3;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/permissionDeniedDeleteProjectRequest.json";
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

    private function projectInBillings(): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id'=>2];

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = 2;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/projectInBillings.json";
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
