<?php

namespace Tests\Api\Tickets;

use DateTime;
use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Tickets\NewTicketService;
use Tests\SuppTest;

class NewTicketServiceTest extends SuppTest
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
        $service = $container->get(NewTicketService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);
        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode());

        unset($responseObj['created_on']);
        unset($expectedObj['created_on']);
        $this->assertSame($expectedObj, $responseObj);
    }
    public function providerTestInvoke(): array
    {
        return [
            $this->validNewTicketBugReport(),
            $this->validNewTicketFeatureReport(),
            $this->validNewTicketChangeOrder()
        ];
    }
    private function validNewTicketBugReport(): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['filter'=>1];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validNewTicketBugReportRequest.json");
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
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validNewTicketBugReportResponse.json";
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

    private function validNewTicketFeatureReport(): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['filter'=>2];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validNewTicketFeatureReportRequest.json");
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
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validNewTicketFeatureReportResponse.json";
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

    private function validNewTicketChangeOrder(): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['filter'=>3];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validNewTicketChangeOrderRequest.json");
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
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validNewTicketChangeOrderResponse.json";
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
     * @param ServerRequestInterface $request
     * @param array $routeArgs
     * @param Container $container
     * @param ResponseInterface $expectedResponse
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @dataProvider providerTestInvokeFailures
     */
    public function test__invokeFailures(ServerRequestInterface $request,
                                         array                  $routeArgs,
                                         Container              $container,
                                         ResponseInterface      $expectedResponse) {

        $service = $container->get(NewTicketService::class);
        $response = $service->__invoke($request, $routeArgs);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        $this->assertSame($expectedObj, $responseObj);

    }

    public function providerTestInvokeFailures(): array
    {
        return [
            $this->missingField(1,"/fixtures/requests/validNonAdminNewTicketMissingTitleRequest.json", "/fixtures/responses/titleRequired.json"),
            $this->missingField(1,"/fixtures/requests/validNonAdminNewTicketMissingCategoryRequest.json","/fixtures/responses/categoryRequired.json"),
            $this->missingField(1,"/fixtures/requests/validNonAdminNewTicketMissingExpectedRequest.json","/fixtures/responses/expectedRequired.json"),
            $this->missingField(1,"/fixtures/requests/validNonAdminNewTicketMissingActualRequest.json","/fixtures/responses/actualRequired.json"),
            $this->missingField(1,"/fixtures/requests/validNonAdminNewTicketMissingStepsReproduceRequest.json","/fixtures/responses/stepsReproduceRequired.json"),
            $this->missingField(2,"/fixtures/requests/validNonAdminNewTicketMissingDescriptionRequest.json","/fixtures/responses/descriptionRequired.json"),
            $this->missingField(3,"/fixtures/requests/validNonAdminNewTicketMissingCurrentFeatureRequest.json","/fixtures/responses/currentFeatureRequired.json"),
            $this->missingField(3,"/fixtures/requests/validNonAdminNewTicketMissingRequiredChangesRequest.json","/fixtures/responses/requiredChangesRequired.json")
        ];
    }

    private function missingField($filter, string $requestJson, string $responseJson): array
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = ['filter'=>$filter];

        $jsonPayload = file_get_contents(dirname(__FILE__) . $requestJson);
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
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        $jsonResponsePath = dirname(__FILE__) . $responseJson;
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



