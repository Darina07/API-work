<?php

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Http\Exception\MethodNotAllowedException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use League\Route\Http\Exception\NotFoundException;

//Initialize to make the IDE happy.
$config_values = null;
$container = null;
$response = null;
$router = null;

include "bootstrap/bootstrap.php";

//$config_values is pulled from config/config.php, and is environment dependent, and should never be added to git / vcs.

$container = getContainer(getConfigValues());

if(handleCors($container->get('config'))) return;

$request = ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$router = getRouter($container, $request);



try {
    $response = $router->dispatch($request);
} catch (NotFoundException $e) {
    $response = new EmptyResponse(404);
} catch (MethodNotAllowedException $e) {
    $response = new EmptyResponse(405);
} catch (RuntimeException $e) {
    $response = new JsonResponse(['error' => "RuntimeException: " . $e->getMessage()], 404);
}

(new SapiEmitter())->emit($response);
