<?php
use App\Controllers\HomeController;
use App\RouterClass;

$router = new RouterClass();
$router->add('GET', '/', [HomeController::class, 'index']);

return $router;