<?php

use DeveoDK\CoreTimeTracker\Controllers\TimeTrackController;
use Illuminate\Routing\Router;

/** @var Router $router */
$router = app(Router::class);

$router->group(['middleware' => 'jwt.validToken'], function () use ($router) {
    $router->post('core/reporter', TimeTrackController::class . '@newTracking');
});
