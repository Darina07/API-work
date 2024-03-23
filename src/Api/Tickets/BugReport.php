<?php

namespace Supp\Api\Tickets;

class BugReport extends BaseNewTicket
{
    protected function postSql(): string
    {
        return <<<SQL
INSERT INTO bug_report (ticket, expected, actual, steps_to_reproduce, solution)
VALUES (:ticket, :expected, :actual, :steps_to_reproduce, :solution);
SQL;
    }

    protected function postValues($requestObj, $ticketID): array
    {
        return [
            "ticket" => $ticketID,
            "expected" => $requestObj['expected'],
            "actual" => $requestObj['actual'],
            "steps_to_reproduce" => $requestObj['steps_to_reproduce'],
            "solution" => $requestObj['solution']
        ];
    }
}