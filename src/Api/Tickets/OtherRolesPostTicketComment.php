<?php

namespace Supp\Api\Tickets;

class OtherRolesPostTicketComment extends BasePostTicketComment
{

    protected function getSql(): string
    {
        return <<<SQL
INSERT INTO comments(ticket, type, created_by, comment)
VALUES (:ticket, :type, :created_by, :comment);
SQL;

    }

    protected function getValues($requestObj, $routeArgs, $type, $container): array
    {
        return [
            "ticket" => $routeArgs['id'],
            "type" => 1,
            "created_by" => $container->get('current_user')->id,
            "comment" => $requestObj['comment']
        ];
    }
}