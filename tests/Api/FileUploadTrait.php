<?php

namespace Tests\Api;

use Hphio\Auth\Models\User;
use Supp\Api\Models\Uploads;
use Exception;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;

trait FileUploadTrait
{

    /**
     * Builds an associative array for an upload file using the RFC-1867 compliant specifications
     * as noted here: https://www.php.net/manual/en/features.file-upload.post-method.php
     *
     * name:      The original name of the file on the client machine.
     * type:      The mime type of the file, if the browser provided this information. An example would be "image/gif".
     *            This mime type is however not checked on the PHP side and therefore don't take its value for granted.
     * size:      The size, in bytes, of the uploaded file.
     * tmp_name:  The temporary filename of the file in which the uploaded file was stored on the server.
     * error:     The error code associated with this file upload.
     * full_path: The full path as submitted by the browser. This value does not always contain a real directory
     *            structure, and cannot be trusted. Available as of PHP 8.1.0.
     *
     * @param $filename
     * @return array
     * @throws \Exception
     */
    protected function loadResourceFile($field, $filename, $error = UPLOAD_ERR_OK) {
        $uploads_dir = __DIR__ . "/Tickets/resources/";
        $source_path = $uploads_dir. $filename;
        if(file_exists($source_path) == false) throw new Exception("$source_path not found. Cannot run test.");

        $tmp_name = tempnam("/tmp","tmp_");
        copy($source_path, $tmp_name);
        /* Get this read to be cleaned after the test. */

        if(file_exists($tmp_name) == false) throw new Exception("$tmp_name not found. Cannot run test.");

        $cmd = escapeshellcmd("mimetype \"$source_path\"");
        $mimetype = trim(explode(":", shell_exec($cmd))[1]);

        $streamOrFile = $tmp_name;
        $size = filesize($tmp_name);
        $errorStatus = 0;
        $clientFilename = $filename;
        $clientMediaType = $mimetype;

        $constructorArgs = [ $streamOrFile,
            $size,
            $errorStatus,
            $clientFilename,
            $clientMediaType
        ];

        $uploadedFile = new UploadedFile($streamOrFile,
            $size,
            $errorStatus,
            $clientFilename,
            $clientMediaType);

        /* Example:
         * clientFilename = 'IMS Logistics - 2021 ONLY.csv'
         * clientMediaType = 'application/octet-stream'
         * error = 0
         * file = /tmp/phpJNu3Wo
         * moved = false
         * size = 295697
         * stream = null
         * */


        $file = [];
        $file[$field] = $uploadedFile;

        return $file;
    }


    /**
     * @param \Supp\Api\Models\Uploads $expectedUpload
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */

    protected function prepFileUpload(Uploads $expectedUpload, ServerRequestInterface $request): array
    {
        $container = $this->buildContainer()->withConfig()->withoutRandomness()
            ->withDatabase()
            ->withDejaVu(1671125400)
            ->getContainer();

        $dbh = $container->get("db");

        $this->fileCleanUp[] = $expectedUpload->storage_path;
        $this->fileCleanUp[] = $request->getUploadedFiles()['uploaded_file'];

        $user = $this->createMock(User::class);
        $user->id = 1;
        $user->role = 1;

        $container->add('current_user', $user);
        $configs = $container->get('config')->get("uploads.path");

        if(file_exists($expectedUpload->storage_path)) unlink($expectedUpload->storage_path);

        $this->assertFalse(file_exists($expectedUpload->storage_path), "$expectedUpload->storage_path is not found.");
        return array($container, $dbh);
    }
}
