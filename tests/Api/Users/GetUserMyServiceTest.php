<?php

namespace Tests\Api\Users;

use Exception;
use Hphio\Auth\Models\User;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Users\GetUserMyService;
use Tests\SuppTest;

class GetUserMyServiceTest extends SuppTest
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
        $service = $container->get(GetUserMyService::class);
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
            $this->validRequest()
        ];
    }

    private function validRequest()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [];

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = '1';

        $container = $this->buildContainer()
            ->withConfig()
            ->withDatabase()
            ->withCurrentUser($user)
            ->getContainer();

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/responses/validRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));

        return [$request, $routeArgs, $container, $expectedResponse];
    }


    /**
     * @param ServerRequestInterface $request
     * @param array $routeArgs
     * @param Container $container
     * @param ResponseInterface $expectedResponse
     * @dataProvider providerTestInvokeFailure
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testInvokeFailure(ServerRequestInterface $request,
                                      array                  $routeArgs,
                                      Container              $container,
                                      ResponseInterface      $expectedResponse){
        $service = $container->get(GetUserMyService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        $this->assertSame($expectedObj, $responseObj);
    }

    public function providerTestInvokeFailure()
    {
        return [
            $this->dbProblem()
        ];
    }

    private function dbProblem()
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [];

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 1;

        /* </mock GET request> */
        $container = $this->buildContainer([QueryRunner::class])
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->getContainer();

        $queryRunner = $this->getMockBuilder(QueryRunner::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                ['run']
            )
            ->getMock();

        $queryRunner->method('run')
            ->willThrowException(new Exception('Database error', 500));

        $container->add(QueryRunner::class, $queryRunner);

        $jsonPayloadPath = dirname(__FILE__) . "/fixtures/responses/dbError.json";
        $jsonPayload = file_get_contents($jsonPayloadPath);
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(500);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));

        return [$request, $routeArgs, $container, $expectedResponse];

    }
}
