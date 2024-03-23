<?php

namespace Tests\Api\TicketCategories;

use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\TicketCategories\GetTicketCategories;
use Tests\SuppTest;

class GetTicketCategoriesTest extends SuppTest
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
        $service = $container->get(GetTicketCategories::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        $this->assertSame($expectedObj, $responseObj);
    }

    public function providerTestInvoke()
    {
        return [
            $this->validRequest(1),
            $this->validRequest(2),
            $this->validRequest(3)
        ];
    }

    private function validRequest($roleId)
    {
        /* <mock GET request> */
        $request = $this->createMock(ServerRequestInterface::class);
        $routeArgs = [];/* </mock GET request> */

        /* <container with user> */

        $user = $this->createMock(User::class);
        $user->id = '1';
        $user->role = $roleId;
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
            ->withConfig()
            ->withDatabase()
            ->withCurrentUser($user)
            ->getContainer();

        /* <expected response template> */
        $jsonPayload = file_get_contents(dirname(__FILE__) . "/fixtures/validAdminRequest.json");
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(200);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));
        /* </expected response template> */

        return [$request, $routeArgs, $container, $expectedResponse];
    }
}
