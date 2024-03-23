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

abstract class BaseNewTicket extends SuppApiService
{
    protected ?Container $container = null;

    abstract protected function postSql() :string;
    abstract protected function postValues($requestObj, $ticketID):array;

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        $requestObj = json_decode($request->getBody()->getContents(), true);
        $this->validateRequest($requestObj, $routeArgs['filter']);
        $ticketID = $this->saveNewTicket($requestObj, $routeArgs['filter']);
        $this->saveTicketInformation($requestObj, $ticketID);
        $data = $this->loadData($ticketID);
        return new JsonResponse($data, 201);
    }

    public function __construct(Container $container) {
        $this->container = $container;
    }

    private function saveNewTicket($requestObj, $filter)
    {
        $sql = <<<SQL
INSERT INTO tickets (title, ticket_type, category, created_by)
VALUES (:title, :ticket_type, :category, :created_by);
SQL;

        $values = [
            "title" => $requestObj['title'],
            "ticket_type" => $filter,
            "category" => $requestObj['category'],
            "created_by" => $this->container->get('current_user')->id
        ];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values)->run();
        return $runner->lastInsertId();
    }

    private function saveTicketInformation($requestObj, $ticketID)
    {
        $sql = $this->postSql();
        $values = $this->postValues($requestObj, $ticketID);
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values)->run();
    }

    private function loadData($ticketID)
    {
        $sql = <<<SQL
SELECT * FROM tickets where id=:ticket_id;
SQL;
        $values = ["ticket_id" => $ticketID];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function validateRequest($requestObj, $filter)
    {
        $this->missingTitle($requestObj);
        $this->missingCategory($requestObj);
        switch ($filter) {
            case 1: //bug report
                $this->missingExpected($requestObj);
                $this->missingActual($requestObj);
                $this->missingStepsReproduce($requestObj);
                break;
            case 2: //feature request
                $this->missingDescription($requestObj);
                break;
            case 3: //change order
                $this->missingCurrentFeature($requestObj);
                $this->missingRequiredChanges($requestObj);
                break;
        }
    }

    private function missingTitle($requestObj)
    {
        if ($requestObj['title'] == "") {
            throw new Exception("Title is required", 400);
        }
    }

    private function missingCategory($requestObj)
    {
        if ($requestObj['category'] == "") {
            throw new Exception("Category is required", 400);
        }
    }

    private function missingExpected($requestObj)
    {
        if ($requestObj['expected'] == "") {
            throw new Exception("Expected is required", 400);
        }
    }

    private function missingActual($requestObj)
    {
        if ($requestObj['actual'] == "") {
            throw new Exception("Actual is required", 400);
        }
    }

    private function missingStepsReproduce($requestObj)
    {
        if ($requestObj['steps_to_reproduce'] == "") {
            throw new Exception("Steps to reproduce is required", 400);
        }
    }

    private function missingRequiredChanges($requestObj)
    {
        if ($requestObj['required_changes'] == "") {
            throw new Exception("Required changes is required", 400);
        }
    }

    private function missingDescription($requestObj)
    {
        if ($requestObj['description'] == "") {
            throw new Exception("Description is required", 400);
        }
    }

    private function missingCurrentFeature($requestObj)
    {
        if ($requestObj['current_feature'] == "") {
            throw new Exception("Current feature is required", 400);
        }
    }


}
