<?php

namespace Supp\Api\Models;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PDO;

class Uploads
{
    /* <database fields> */

    public $id = null;
    public $original_file_name = null;
    public $storage_path = null;
    public $url = null;
    public $uploaded_by = null;
    public $mime_type = null;
    public $created_on = null;
    public $upload_type = null;
    public $ticket = null;
    public $comment = null;
    public $file_hash = null;

    /* </database fields> */


    /* <Dependency Injection Fields> */

    public ?Container $container = null;

    /* </Dependency Injection Fields> */

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Returns an associative array of values for this class.
     * @return array
     */

    public function getMyValues(): array
    {
        return [
            "original_file_name" => $this->original_file_name
            ,
            "storage_path" => $this->storage_path
            ,
            "url" => $this->url
            ,
            "uploaded_by" => $this->uploaded_by
            ,
            "mime_type" => $this->mime_type
            ,
            "upload_type" => $this->upload_type
            ,
            "ticket" => $this->ticket
            ,
            "comment" => $this->comment
            ,
            "file_hash" => $this->file_hash
        ];
    }

    public function insert($insertError = null)
    {
        $sql = " INSERT INTO `uploads`
                ( `original_file_name`
                , `storage_path`
                , `url`
                , `uploaded_by`
                , `mime_type`
                , `upload_type`
                , `ticket`
                , `comment`
                , `file_hash`
                )
                VALUES
                ( :original_file_name
                , :storage_path
                , :url
                , :uploaded_by
                , :mime_type
                , :upload_type
                , :ticket
                , :comment
                , :file_hash
                )";
        $values = $this->getMyValues();
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values)->run();
        $this->id = $runner->lastInsertId();

        return $this->id;
    }

    public function update($updateError = null)
    {
        $sql = "UPDATE `uploads`
                SET
                `original_file_name` = :original_file_name,
                `storage_path` = :storage_path,
                `url` = :url,
                `uploaded_by` = :uploaded_by,
                `mime_type` = :mime_type,
                `upload_type` = :upload_type,
                `ticket` = :ticket,
                `comment` = :comment,
                `file_hash` = :file_hash
                WHERE `id` = :id
                LIMIT 1";

        $values = $this->getMyValues();
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values)->run();
    }


    public function get($upload_id)
    {
        $sql = "SELECT * FROM uploads WHERE id = :upload_id";
        $values = ['upload_id' => $upload_id];

        $runner = $this->container->get(QueryRunner::class);
        $stmt = $runner->useQuery($sql)->withValues($values)->run();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
