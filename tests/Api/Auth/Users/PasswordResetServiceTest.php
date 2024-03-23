<?php

namespace Tests\Api\Auth\Users;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Auth\Users\PasswordResetService;
use Tests\SuppTest;

class PasswordResetServiceTest extends SuppTest
{

    /**
     * @return void
     * @dataProvider providerTestInvoke
     * @todo Add rejection for bad emails (400)
     * @todo Add rejection for email not found (404).
     */
    public function test__invoke(ServerRequestInterface $request, array $routeArgs, Container $container, $expectedResponse)
    {
        $service = $container->get(PasswordResetService::class);
        $response = $service->__invoke($request, $routeArgs);
        $requestJson = json_decode((string)$request->getBody(), true);

        //200 OK = user found, email sent. 404 = user not found. No email sent.
        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode());

        $stmt = $container->get('db')->prepare("SELECT nonce FROM users WHERE email = :email");
        $values = ['email' => $requestJson['email']];
        $stmt->execute($values);
        $this->assertSame('00000', $stmt->errorCode(), implode("|", $stmt->errorInfo()));

        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $this->assertSame('9f052830-c39a-4bac-81a2-64bf78c38030', $row->nonce);

    }

    public function providerTestInvoke()
    {
        return [
            $this->validPasswordRequest()
        ];
    }

    private function validPasswordRequest()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn('{"email":"kaloyan@hph.io"}');

        $routeArgs = [];

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);

      $container = $this->buildContainer()
        ->withoutRandomness()
        ->withDatabase()
        ->withConfig()
        ->getContainer();

        return [ $request, $routeArgs, $container, $expectedResponse ];
    }

    /**
     * @return void
     * @dataProvider providerTestInvokeFailures
     */
    public function testInvokeFailures(ServerRequestInterface $request,
                                       array                  $routeArgs,
                                       Container              $container,
                                       ResponseInterface      $expectedResponse) {
        $service = $container->get(PasswordResetService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode());

        $keys = array_keys($expectedObj);
        $responseKeys = array_keys($responseObj);

        $this->assertEquals(count($keys), count($responseKeys));

        foreach ($keys as $key) {
            $this->assertSame($expectedObj[$key], $responseObj[$key]);
        }
    }

    public function providerTestInvokeFailures(): array
    {
        return [
            $this->nonExistingEmail()
        ];
    }

    private function nonExistingEmail(): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn('{"email":"xxx@yuo.com"}');

        $routeArgs = [];

        $response = ['error' => 'This e-mail address is not registered in the system'];
        $responseJsonPayload = json_encode($response);

        $responseStream = fopen('php://memory', 'r+');
        fwrite($responseStream, $responseJsonPayload);
        rewind($responseStream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(404);

        $expectedResponse->method('getBody')->willReturn(new Stream($responseStream));

        $container = $this->buildContainer()
            ->withoutRandomness()
            ->withDatabase()
            ->withConfig()
            ->getContainer();

        return [ $request, $routeArgs, $container, $expectedResponse ];
    }
}
