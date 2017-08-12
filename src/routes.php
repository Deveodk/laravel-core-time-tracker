<?php

use DeveoDK\CoreTimeTracker\Controllers\TimeTrackController;
use Illuminate\Routing\Router;

/** @var Router $route */
$route = app(Router::class);

$route->post('core/timetrack', TimeTrackController::class.'@newTracking');

