<?php
namespace Supp\Api\Users;

use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Supp\SuppApiService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetUsersService extends SuppApiService
{

    public function __invoke(ServerRequestInterface $request, array $routeArgs = []): ResponseInterface
    {
        try {
            $service = GetUsersServiceFactory::getUsers($this->container);
            $data = $service->getUsers($request, $routeArgs);
        } catch (Exception $e) {
            return $this->returnException($e);
        }

        return new JsonResponse($data, 200);
    }
}