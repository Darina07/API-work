<?php

namespace Supp\Api\Tickets;

use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

abstract class BasePostTicketComment extends SuppApiService
{
    protected ?Container $container = null;

    abstract protected function getSql() :string;
    abstract protected function getValues($requestObj, $routeArgs, $type, $container):array;

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        $requestObj = json_decode($request->getBody()->getContents(), true);

        if($this->container->get('current_user')->role != 1 and $this->container->get('current_user')->role != 2 and $routeArgs['filter'] == "internal"){
            return new JsonResponse(["error"=>"You do not have permissions to do that"], 403);
        }

        $commentID = $this->saveNewTicketComment($requestObj, $routeArgs, $this->container);
        $data = $this->loadData($commentID);
        return new JsonResponse($data, 201);
    }

    public function __construct(Container $container) {
        $this->container = $container;
    }

    private function saveNewTicketComment($requestObj, $routeArgs, $container)
    {
        $sql = $this->getSql();
        switch ($routeArgs['filter']){
            case "reply":
                $type = 1;
                break;
            case "internal":
                $type = 2;
                break;
            default:
                throw new \Exception("No such filter", 400);
        }
        $values = $this->getValues($requestObj, $routeArgs, $type, $container);
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values)->run();
        return $runner->lastInsertId();
    }

    private function loadData($commentID)
    {
        $sql = <<<SQL
SELECT * FROM comments where id=:comment_id;
SQL;
        $values = ["comment_id" => $commentID];
        $runner = $this->container->get(QueryRunner::class);
        $runner->useQuery($sql)->withValues($values);
        $stmt = $runner->run();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




}
