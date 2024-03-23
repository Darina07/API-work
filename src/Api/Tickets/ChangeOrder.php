<?php

namespace Supp\Api\Tickets;

class ChangeOrder extends BaseNewTicket
{
    protected function postSql(): string
    {
        return <<<SQL
INSERT INTO change_orders(ticket, title, current_feature, required_changes)
VALUES (:ticket, :title, :current_feature, :required_changes);
SQL;

    }

    protected function postValues($requestObj, $ticketID): array
    {
        return [
            "ticket" => $ticketID,
            "title" => $requestObj['title'],
            "current_feature" => $requestObj['current_feature'],
            "required_changes" => $requestObj['required_changes']
        ];
    }
}