<?php

namespace Supp\Api\Tickets;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Supp\SuppApiService;

class GetAllTicketsService extends SuppApiService
{

  /**
   * @param ServerRequestInterface $request
   * @param array $routeArgs
   * @return ResponseInterface
   */
  public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
  {
    try {
      $service = GetAllTicketsServiceFactory::getTicket($this->container);
      return $service->__invoke($request, $routeArgs);
    } catch (Exception $e) {
      return $this->returnException($e);
    }
  }
}












