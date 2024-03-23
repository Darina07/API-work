<?php

namespace Supp\Api\Tickets;

use Exception;
use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

abstract class BaseTicketComments extends SuppApiService
{
    protected ?Container $container = null;

    abstract protected function getSql() :string;
    abstract protected function getValues($ticketID):array;

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        $data = $this->getAllTicketComments($routeArgs['id']);

        foreach($data as $key => $value) {
            $commentId = $data[$key]['id'];
            $data[$key]['uploads'] = $this->getUploadsData($commentId);
            if (!empty($data[$key]['uploads'])) {
                $data[$key]['uploads'] = $this->setBase64Encode($data[$key]['uploads']);
            }
        }
        return new JsonResponse($data, 200);
    }

    public function __construct(Container $container) {
        $this->container = $container;
    }

    private function getAllTicketComments($ticketID)
    {
        $sql = $this->getSql();
        $values = $this->getValues($ticketID);
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

       if ($this->checkifTicketExists($ticketID) == 0) throw new Exception("Ticket does not exist.", 400);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function checkIfTicketExists($ticketID)
    {
        $sql = <<<SQL
SELECT id FROM tickets WHERE id = :ticket_id
SQL;

        $values = [
            "ticket_id" => $ticketID
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        return $stmt->rowCount();
    }
    private function getUploadsData($commentId)
    {
        $sql = <<<SQL
SELECT  up.original_file_name,
        up.storage_path,
        up.url
FROM comments c
JOIN uploads up on c.id = up.comment
WHERE c.id=:comment_id;
SQL;

        $values = [
            "comment_id" => $commentId
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    private function setBase64Encode($data)
    {
        foreach($data as $key => $value)
        {
            // trying to fix a bug not sure whether it should be like that
            if(file_exists($data[$key]['storage_path'])) {
                $imagedata = file_get_contents($data[$key]['storage_path']);
                $base64 = base64_encode($imagedata);
                $data[$key]['image'] = 'data:image/jpeg;base64,'. $base64;
            }

        }
        return $data;
    }
}
