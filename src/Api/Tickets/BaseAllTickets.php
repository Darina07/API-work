<?php

namespace Supp\Api\Tickets;

use Hphio\Utils\QueryRunner;
use Laminas\Diactoros\Response\JsonResponse;
use League\Container\Container;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\SuppApiService;

abstract class BaseAllTickets extends SuppApiService
{

  protected ?Container $container = null;

  abstract protected function getSql() :string;
  abstract protected function getValues():array;

  public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
  {
    $data = $this->getAllTickets();
    return new JsonResponse($data, 200);
  }

  public function __construct(Container $container) {
    $this->container = $container;
  }

  private function getAllTickets()
  {
    $sql = $this->getSql();
    $values = $this->getValues();
    $runner = $this->container->get(QueryRunner::class);
    $runner->useQuery($sql)->withValues($values);
    $stmt = $runner->run();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }

}