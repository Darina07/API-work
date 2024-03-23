<?php

namespace Supp\Api\Tickets;

use Hphio\Utils\QueryRunner;
use League\Container\Container;
use Psr\Http\Message\ServerRequestInterface;

class SuperAdminAssignTicket implements AssignTicketInterface
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function assignTicket(ServerRequestInterface $request, array $routeArgs = [])
    {
        $requestObj = json_decode($request->getBody()->getContents(), true);
        $this->assignTicketF($routeArgs['id'], $requestObj['user']);
    }

    private function assignTicketF($ticket, $user)
    {
        $sql = <<<SQL
UPDATE tickets
SET assignee = :user_id
WHERE id = :ticket_id
SQL;

        $values = [
            'user_id' => $user,
            'ticket_id' => $ticket
        ];

        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $runner->run();
    }
}