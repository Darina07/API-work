<?php

namespace Tests\Api\Billings\Export;

use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Billings\Export\GetAllBillingsExportService;
use Tests\SuppTest;


class GetAllBillingsExportServiceTest extends SuppTest
{
  /**
   * @param ServerRequestInterface $request
   * @param array $routeArgs
   * @param Container $container
   * @param ResponseInterface $expectedResponse
   * @return void
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   * @dataProvider providerTestInvoke
   */
  public function test__invoke(ServerRequestInterface $request,
                               array                  $routeArgs,
                               Container              $container,
                               ResponseInterface      $expectedResponse)
  {
    $this->loadDataSet();
    $service = $container->get(GetAllBillingsExportService::class);
    $response = $service->__invoke($request, $routeArgs);

    $this->assertInstanceOf(JsonResponse::class, $response);

    $responseObj = json_decode($response->getBody()->getContents(), true);
    $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

    $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

    $this->assertSame($expectedObj, $responseObj);
  }

  public function providerTestInvoke (): array
  {
    return [
        $this->getAllExportsBillings()
    ];
  }

  private function getAllExportsBillings ()
  {
    $request = $this->createMock(ServerRequestInterface::class);
    $routeArgs = [];

    $user = $this->createMock(User::class);
    $user->id = '1';
    $user->role = '1';

    $container = $this->buildContainer()
        ->withCurrentUser($user)
        ->withConfig()
        ->withDatabase()
        ->getContainer();

    $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/getAllExportsBillings.json";
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
}
