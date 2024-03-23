<?php

namespace Supp\Api\Tickets;

class OtherRolesTicketComments extends BaseTicketComments
{

    protected function getSql(): string
    {
        return <<<SQL
SELECT c.id,
       c.ticket,
       c.type,
       CONCAT(u.first, " ", u.last)as created_by,
       c.created_on,
      c. comment
FROM comments c
JOIN users u on c.created_by = u.id
WHERE c.ticket=:ticket_id and c.type=1;
SQL;
    }

    protected function getValues($ticketID): array
    {
        return ["ticket_id" => $ticketID];
    }
}