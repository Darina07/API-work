<?php

namespace Tests\Api\Tickets;

use DateTime;
use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Tickets\TicketCommentsService;
use Tests\SuppTest;

class TicketCommentsServiceTest extends SuppTest
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
        $service = $container->get(TicketCommentsService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);
        foreach($responseObj as $key => $value)
        {
            unset($responseObj[$key]['uploads']);
            unset($expectedObj[$key]['uploads']);
        }
        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode());

        $this->assertSame($expectedObj, $responseObj);
    }
    public function providerTestInvoke()
    {
        return [
            $this->validAdminTicketCommentsRequest(),
            $this->validNonAdminTicketCommentsRequest()
        ];
    }
    private function validAdminTicketCommentsRequest(): array
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id'=>3];
        /* </mock GET request> */

        /* <container with user> */
        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 1;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        /* </container with user> */



        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validAdminTicketCommentsRequestResponse.json";
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

    private function validNonAdminTicketCommentsRequest()
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['id'=>3];
        /* </mock GET request> */

        /* <container with user> */
        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 3;

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        /* </container with user> */

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validNonAdminTicketCommentsRequestResponse.json";
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

    $service = $container->get(TicketCommentsService::class);
    $response = $service->__invoke($request, $routeArgs);

    $responseObj = json_decode($response->getBody()->getContents(), true);
    $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

    $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

    $this->assertSame($expectedObj, $responseObj);

  }

  public function providerTestInvokeFailures()
  {
    return [
        $this->noExistingTicket()
    ];
  }

  private function noExistingTicket ()
  {
    /* <mock GET request> */
    $request = $this->createMock(ServerRequestInterface::class);
    $routeArgs = ['id'=>4];
    /* </mock GET request> */

    /* <container with user> */
    $user = $this->createMock(User::class);
    $user->id = '1';
    $user->role = 3;

    $container = $this->buildContainer()
        ->withCurrentUser($user)
        ->withConfig()
        ->withDatabase()
        ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
        ->getContainer();

    /* </container with user> */

    $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/noExistingTicket.json";
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




