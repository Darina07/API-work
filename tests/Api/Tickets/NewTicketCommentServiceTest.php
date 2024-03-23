<?php

namespace Tests\Api\Tickets;

use DateTime;
use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Tickets\NewTicketCommentService;
use Tests\SuppTest;

class NewTicketCommentServiceTest extends SuppTest
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
        $service = $container->get(NewTicketCommentService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);
     //   $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode());

        unset($expectedObj['created_on']);
        unset($responseObj['created_on']);
        $this->assertSame($expectedObj, $responseObj);
    }
    public function providerTestInvoke()
    {
        return [
            $this->validAdminNewTicketComment(1),
            $this->validAdminNewTicketComment(2),
            $this->validAdminNewTicketCommentReply(1),
            $this->validAdminNewTicketCommentReply(2),
            $this->validNonAdminNewTicketComment()
        ];
    }
    private function validAdminNewTicketComment($roleId): array
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [
            'id'=>3,
            'filter'=>'internal'
        ];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validAdminNewTicketCommentRequest.json");
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

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        /* </container with user> */


        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validAdminNewTicketCommentResponse.json";
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

    private function validAdminNewTicketCommentReply($roleId): array
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [
            'id'=>3,
            'filter'=>'reply'
        ];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validAdminNewTicketCommentRequest.json");
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

        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        /* </container with user> */


        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validAdminNewTicketCommentReplyResponse.json";
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

    private function validNonAdminNewTicketComment()
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [
                'id'=>3,
                'filter'=>'reply'
        ];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validNonAdminNewTicketCommentRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        /* </mock GET request> */

        /* <container with user> */
        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 3;
        $user->parent_entity = '11';


        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        /* </container with user> */

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/validNonAdminNewTicketCommentResponse.json";
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

        $service = $container->get(NewTicketCommentService::class);
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
            $this->wrongFilter()
        ];
    }

    private function nonAdminInternal()
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [
            'id'=>3,
            'filter'=>'internal'
        ];

        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/requests/validNonAdminNewTicketCommentRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $request->method('getBody')->willReturn(new Stream($stream));

        /* </mock GET request> */

        /* <container with user> */
        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = 3;
        $user->parent_entity = '11';


        $container = $this->buildContainer()
            ->withCurrentUser($user)
            ->withConfig()
            ->withDatabase()
            ->withDejaVu((new DateTime('2023-01-26'))->getTimestamp())
            ->getContainer();

        /* </container with user> */

        $jsonResponsePath = dirname(__FILE__) . "/fixtures/responses/noPermissionAdminInternal.json";
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

    private function wrongFilter()
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [
            'id' => 3,
            'filter'=>"test123"
        ];
        /* </mock GET request> */

        $jsonPayloadPath = dirname(__FILE__) . "/fixtures/requests/validNonAdminNewTicketCommentRequest.json";
        $jsonPayload = file_get_contents($jsonPayloadPath);
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);
        $request->method('getBody')->willReturn(new Stream($stream));

        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->id = 1;
        $user->role = 3;
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
            ->getContainer();

        /* </container with user> */


        $jsonPayloadPath = dirname(__FILE__) . "/fixtures/responses/wrongFilterError.json";
        $jsonPayload = file_get_contents($jsonPayloadPath);
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(400);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));

        return [$request, $routeArgs, $container, $expectedResponse];

    }

}




