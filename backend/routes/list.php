<?php

require_once __DIR__ . '/Routes/RequestHandler.php';
require_once __DIR__ . '/Middlewares/ApiMiddleware.php';

use Routes\RequestHandler;

$handler = new RequestHandler();

$handler->group(['prefix' => '/api', 'middlewares' => [new ApiMiddleware()]], function (RequestHandler $router) {
    $router->group(['prefix' => '/addresses'], function (RequestHandler $router) {
        $router->addRoute('GET', '/export/xml', AddressBook\Controllers\AddressBook\ExportXmlController::class);
        $router->addRoute('GET', '/export/json', AddressBook\Controllers\AddressBook\ExportJsonController::class);

        $router->addRoute('GET', '', AddressBook\Controllers\AddressBook\IndexController::class);
        $router->addRoute('GET', '/:id', AddressBook\Controllers\AddressBook\ShowController::class);
        $router->addRoute('POST', '', AddressBook\Controllers\AddressBook\StoreController::class);
        $router->addRoute('PUT', '/:id', AddressBook\Controllers\AddressBook\UpdateController::class);
        $router->addRoute('DELETE', '/:id', AddressBook\Controllers\AddressBook\DestroyController::class);
    });

    $router->group(['prefix' => '/cities'], function (RequestHandler $router) {
        $router->addRoute('GET', '', AddressBook\Controllers\Cities\IndexController::class);
    });

    $router->group(['prefix' => '/groups'], function (RequestHandler $router) {
        $router->addRoute('GET', '', AddressBook\Controllers\Groups\IndexController::class);
        $router->addRoute('GET', '/:id', AddressBook\Controllers\Groups\ShowController::class);
        $router->addRoute('POST', '', AddressBook\Controllers\Groups\StoreController::class);
        $router->addRoute('PUT', '/:id', AddressBook\Controllers\Groups\UpdateController::class);
        $router->addRoute('DELETE', '/:id', AddressBook\Controllers\Groups\DestroyController::class);
    });

    $router->group(['prefix' => '/tags'], function (RequestHandler $router) {
        $router->addRoute('GET', '/active', AddressBook\Controllers\Tags\ActiveController::class);
        $router->addRoute('GET', '', AddressBook\Controllers\Tags\IndexController::class);
    });
});

$handler->handleRequest();