<?php

namespace Tests\Api\Billings\Export;

use DateTime;
use Exception;
use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Billings\Export\ExportBillingService;
use Supp\Api\Billings\Export\ExportBillingServiceFactory;
use Supp\Api\Users\Roles;
use Tests\SuppTest;

class ExportBillingServiceTest extends SuppTest
{

  /**
   * @return void
   * @dataProvider providerTestInvoke
   */
  public function test__invoke(ServerRequestInterface $request, array $routeArgs, Container $container, ResponseInterface $expectedResponse)
  {
    $this->loadDataSet();
    $service = $container->get(ExportBillingService::class);
    $response = $service->__invoke($request, $routeArgs);

    $responseObj = json_decode($response->getBody()->getContents(), true);
    $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

    $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));
    $this->assertSame($expectedObj, $responseObj);
  }

  public function providerTestInvoke()
  {

    $reports = [
        'billings-report-today.csv',  'billings-report-yesterday.csv',
        'billings-report-this-week.csv',  'billings-report-last-week.csv',
        'billings-report-this-month.csv', 'billings-report-last-month.csv',
        'billings-report-last-30days.csv', 'billings-report-last-90days.csv',
        'billings-report-last-12months.csv', 'billings-report-this-year.csv',
        'billings-report-last-year.csv', 'billings-report-this-week.csv'
     ];

    $filter = 1;
    foreach($reports as $report) {
      $reportsList[] = $this->BillingsExport($report,$filter);
      $filter++;
    }

    return $reportsList;
  }

  private function BillingsExport ($report,$filter)
  {
    $request = $this->createMock(ServerRequestInterface::class);
    $routeArgs = ['id' => $filter];

    $serverParams = [];
    $serverParams["SERVER_NAME"] = "dev.support.hph.io";
    $serverParams["SERVER_ADDR"] = "127.0.0.1";
    $serverParams["SERVER_PORT"] = "443";
    $serverParams["REMOTE_ADDR"] = "127.0.0.1";
    $serverParams["REQUEST_SCHEME"] = "https";

    $jsonPayload = '{}';

    $mockStream = fopen('php://memory', 'r+');
    fwrite($mockStream, $jsonPayload);
    rewind($mockStream);

    $request->method('getBody')->willReturn(new Stream($mockStream));
    $request->method('getServerParams')->willReturn($serverParams);

    $container = $this->buildContainer()
    ->withConfig()
    ->withDatabase()
    ->withDejaVu((new DateTime('2023-05-19'))->getTimestamp())
    ->getContainer();

    $user = $this->createMock(User::class);
    $user->id = 1;
    $user->role = 1;

    $container->add('current_user', $user);

    $responseJsonPayload = '{"url":"https://dev.support.hph.io/downloads/reports/'.$report.'"}';

    $responseStream = fopen('php://memory', 'r+');
    fwrite($responseStream, $responseJsonPayload);
    rewind($responseStream);

    $expectedResponse = $this->createMock(ResponseInterface::class);
    $expectedResponse->method('getStatusCode')->willReturn(200);
    $expectedResponse->method('getBody')->willReturn(new Stream($responseStream));

    return [$request, $routeArgs, $container, $expectedResponse];
  }
  

    /**
     * @param ServerRequestInterface $request
     * @param array $routeArgs
     * @param Container $container
     * @param ResponseInterface $expectedResponse
     * @dataProvider providerTestInvokeFailure
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
  public function testInvokeFailure(ServerRequestInterface $request,
                                    array                  $routeArgs,
                                    Container              $container,
                                    ResponseInterface      $expectedResponse){
      $service = $container->get(ExportBillingService::class);
      $response = $service->__invoke($request, $routeArgs);

      $this->assertInstanceOf(JsonResponse::class, $response);

      $responseObj = json_decode($response->getBody()->getContents(), true);
      $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

      $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

      $this->assertSame($expectedObj, $responseObj);
  }

    /**
     * @return array
     */
    public function providerTestInvokeFailure(): array
    {
        return [
            $this->permissionDenied()
        ];
    }

    /*
     * Test that client cannot export billings.
     * Expected message: Permission denied.
     */
    private function permissionDenied(): array
    {/* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id' => 1];
        /* </mock GET request> */

        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->id = 2;
        $user->role = Roles::Client->roleId();

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->getContainer();
        /* </container with user> */

        $jsonPayloadPath = dirname(__FILE__) . "/fixtures/responses/permissionDenied.json";
        $this->assertFileExists($jsonPayloadPath);
        $jsonPayload = file_get_contents($jsonPayloadPath);
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(403);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));

        return [$request, $routeArgs, $container, $expectedResponse];
    }

}
