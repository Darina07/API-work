<?php

namespace Supp\Api\Users;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \Supp\SuppApiService;

class UserBillingsService extends SuppApiService
{

    /**
     * @param ServerRequestInterface $request
     * @param array $routeArgs
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = UserBillingsServiceFactory::getBillings($this->container);
            $data = $service->getBillings($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }

        return new JsonResponse($data, 200);
    }

}
