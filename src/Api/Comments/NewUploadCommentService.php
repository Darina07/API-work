<?php

namespace Supp\Api\Comments;

use DateTime;
use Exception;
use hphio\util\RandomGenerator;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\Api\Models\Uploads;
use Supp\SuppApiService;

class NewUploadCommentService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            list($storage_path, $extension, $uuid, $uploadedFile) = $this->moveToStorage($request);
        } catch (Exception $exception) {
            return new JsonResponse(["error" => $exception->getMessage()], 400);
        }
        $upload = $this->container->get(Uploads::class);
        $upload->original_file_name = $uploadedFile->getClientFilename();
        $upload->storage_path = $storage_path;
        $upload->url = sprintf(
            "%s://%s/uploads/%s.%s",
            $request->getServerParams()['REQUEST_SCHEME'],
            $request->getServerParams()['SERVER_NAME'],
            $uuid
            ,
            $extension
        );
        $upload->uploaded_by = $this->container->get('current_user')->id;
        $upload->mime_type = $uploadedFile->getClientMediaType();
        $upload->upload_type = $request->getParsedBody()['upload_type'];
        $upload->ticket = null;
        $upload->comment = $routeArgs['id'];
        $upload->file_hash = sha1_file($upload->storage_path);

        try {
            $id = (int)$upload->insert();
        } catch (Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }

        $upload->id = $id;
        return $this->uploadFile($upload);
    }

    private function uploadFile($upload)
    {
        $current_user = $this->container->get('current_user');

        $data = $this->packageData($upload, $current_user);
        return new JsonResponse($data, 201);
    }

    private function packageData($upload, $current_user)
    {
        $data = [];
        $data['id'] = $upload->id;
        $data['original_file_name'] = $upload->original_file_name;
        $data['url'] = $upload->url;
        $data['created_on'] = (new DateTime($upload->created_on))->format("m/d/Y @ h:i A");
        $data['mime_type'] = $upload->mime_type;
        $data['uploaded_by'] = $this->container->get('current_user')->id;
        $data['uploaded_by_name'] = sprintf("%s %s", $current_user->first, $current_user->last);
        $data['comment'] = $upload->comment;
        return $data;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function moveToStorage(ServerRequestInterface $request)
    {
        try {
            $files = $request->getUploadedFiles();
            $uploadedFile = $files['uploaded_file'];
        } catch (Exception $e) {
            if ($e->getMessage() == "Undefined index: uploaded_file") {
            }
            throw new Exception("Could not retrieve uploaded file. Did you upload using the field name 'uploaded_file'?");
            return 1;
        }

        $uploadPath = $this->container->get('config')->get("uploads.path");
        $uuid = $this->container->get(RandomGenerator::class)->uuidv4();

        $buffer = explode(".", $uploadedFile->getClientFilename());
        $extension = array_pop($buffer);
        $finalDestination = $uploadPath . "/" . $uuid . "." . $extension;
        $uploadedFile->moveTo($finalDestination);
        return [$finalDestination, $extension, $uuid, $uploadedFile];
    }
}
