<?php

namespace Supp\Api\Tickets;

class FeatureRequest extends BaseNewTicket
{

    protected function postSql(): string
    {
        return <<<SQL
INSERT INTO feature_requests (ticket, title, description)
VALUES (:ticket, :title, :description);
SQL;

    }

    protected function postValues($requestObj, $ticketID): array
    {
        return [
            "ticket" => $ticketID,
            "title" => $requestObj['title'],
            "description" => $requestObj['description']
        ];
    }
}