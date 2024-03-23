<?php

namespace Tests\Api\Comments;

use Hphio\Auth\Models\User;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Stream;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Comments\NewUploadCommentService;
use Supp\Api\Models\Uploads;
use Tests\Api\FileUploadTrait;
use Tests\SuppTest;

class NewUploadCommentServiceTest extends SuppTest
{
    use FileUploadTrait;
    /**
     * @return void
     * @dataProvider providerTestInvoke
     */
    public function test__invoke(ServerRequestInterface $request,
                                 array                  $routeArgs,
                                 Container              $container,
                                 ResponseInterface      $expectedResponse,
                                 Uploads $expectedUpload)
    {
        $this->loadDataSet();
        $this->prepFileUpload($expectedUpload, $request);

        $service = $container->get(NewUploadCommentService::class);
        $response = $service->__invoke($request, $routeArgs);

        $this->assertSame($expectedUpload->upload_type, $request->getParsedBody()['upload_type']);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $responseObj = json_decode($response->getBody()->getContents(), true);
        $expectedObj = json_decode($expectedResponse->getBody()->getContents(), true);

        $pattern = '%[0-9]{2}/[0-9]{2}/[0-9]{4} @ [0-9]{2}:[0-9]{2} (AM|PM)%m';
        $this->assertSame(1, preg_match($pattern, $responseObj['created_on']));

        unset($responseObj['created_on']);
        unset($expectedObj['created_on']);

        $this->assertSame($expectedResponse->getStatusCode(), $response->getStatusCode(), print_r($responseObj, true));

        $this->assertSame($expectedObj, $responseObj, print_r($responseObj, true));
    }

    public function providerTestInvoke()
    {
        return[
            $this->validRequest()
        ];
    }

    private function validRequest()
    {
        $currentUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();

        $currentUser->first = "Kaloyan";
        $currentUser->last = "Stoyanov";
        $currentUser->id = 1;

        $container = $this->buildContainer()
            ->withDatabase()
            ->withConfig()
            ->withoutRandomness()
            ->withDejaVu()
            ->withCurrentUser($currentUser)
            ->getContainer();

        $configs = $container->get('config')->get("uploads.path");

        $files = $this->loadResourceFile('uploaded_file', "test.jpg");
        $post = ['upload_type' => 'comment'];

        $headers = [
            'Content-Length' => $files['uploaded_file']->getSize()
        ];

        $this->addServerParam('SERVER_NAME', "dev.support.hph.io");
        $this->addServerParam('REQUEST_SCHEME', "https");

        $request = $this->buildMockServerRequest()
            ->withFiles($files)
            ->withPost($post)
            ->withHeaders($headers)
            ->getRequest();

        $expectedUpload = $this->createStub(Uploads::class);
        $expectedUpload->id = 2;
        $expectedUpload->original_file_name = "test.jpg";
        $expectedUpload->storage_path = $configs . "/9f052830-c39a-4bac-81a2-64bf78c38030.jpg";
        $expectedUpload->url = "https://dev.support.hph.io/uploads/9f052830-c39a-4bac-81a2-64bf78c38030.jpg";
        $expectedUpload->uploaded_by = 1;
        $expectedUpload->mime_type = "image/jpeg";
        $expectedUpload->ticket = null;
        $expectedUpload->comment = 1;
        $expectedUpload->upload_type = "comment";

        $routeArgs = ['id' => 1];
        $jsonPayload = '{"id":2,"original_file_name":"test.jpg","url":"https://dev.support.hph.io/uploads/9f052830-c39a-4bac-81a2-64bf78c38030.jpg","created_on":"04/07/2022 @ 10:21 AM","mime_type":"image/jpeg","uploaded_by":1,"uploaded_by_name":"Kaloyan Stoyanov","comment":1}';
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $jsonPayload);
        rewind($stream);

        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedResponse->method('getStatusCode')->willReturn(201);
        $expectedResponse->method('getBody')->willReturn(new Stream($stream));
        return [$request, $routeArgs, $container, $expectedResponse, $expectedUpload];
    }
}

