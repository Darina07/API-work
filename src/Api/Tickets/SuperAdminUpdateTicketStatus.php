<?php

namespace Supp\Api\Tickets;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use PHPUnit\Util\Exception;
use Psr\Http\Message\ServerRequestInterface;

class SuperAdminUpdateTicketStatus implements UpdateTicketStatusInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function updateTicketStatus(ServerRequestInterface $request, array $routeArgs = [])
    {
        $requestObj = json_decode($request->getBody()->getContents(), true);

        if (!empty($requestObj['comment_id']) && $this->checkTicketForComments($requestObj['comment_id']) >= 1) {
            $this->changeTicketStatus($routeArgs['id'], $requestObj['status']);
            $this->saveTicketProgress($routeArgs['id'], $requestObj['status'], $requestObj['comment_id']);
        }
        else {
            throw new Exception("Changing a ticket's status requires a comment.",400);
        }
    }

    private function changeTicketStatus($ticket_id, $status)
    {
        $sql = <<<SQL
UPDATE tickets
SET status = :status
WHERE id = :ticket_id
SQL;

        $values = [
            'status' => $status,
            'ticket_id' => $ticket_id
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $runner->run();
    }

    private function saveTicketProgress($ticket_id, $status, $comment_id)
    {
        $sql = <<<SQL
INSERT INTO ticket_progress(ticket_id, status, user_id, comment_id) VALUES (:ticket_id, :status, :user_id, :comment_id);
SQL;

        $values = [
            'ticket_id' => $ticket_id,
            'status' => $status,
            'user_id' => $this->container->get('current_user')->id,
            'comment_id' => $comment_id
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $runner->run();
    }

    private function checkTicketForComments($comment_id)
    {
        $sql = <<<SQL
SELECT * FROM comments where id = :comment_id;
SQL;

        $values = [
            'comment_id' => $comment_id
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();
        return $stmt->rowCount();
    }
}