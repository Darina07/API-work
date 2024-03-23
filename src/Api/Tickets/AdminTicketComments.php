<?php

namespace Supp\Api\Tickets;

class AdminTicketComments extends BaseTicketComments
{

    protected function getSql(): string
    {
        return <<<SQL
SELECT c.id,
       c.ticket,
       c.type,
       CONCAT(u.first, " ", u.last)as created_by,
       c.created_on,
       c. comment,
       ts.name as status_name
FROM comments c
JOIN users u on c.created_by = u.id
LEFT JOIN ticket_progress tp on c.id = tp.comment_id
LEFT JOIN ticket_status ts on tp.status = ts.id
WHERE c.ticket=:ticket_id;
SQL;
    }

    protected function getValues($ticketID): array
    {
        return ["ticket_id" => $ticketID];
    }
}