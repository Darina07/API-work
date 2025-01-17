<?php

namespace Supp\Api\Tickets;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class GetTicketsByCategoryService extends SuppApiService
{
    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = GetTicketsByCategoryServiceFactory::getService($this->container);
            $data = $service->getTicketsByCategory($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }

        return new JsonResponse($data, 200);
    }


}
