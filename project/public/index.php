<?php
require_once __DIR__ . '/../autoload.php';

$router = require __DIR__ . '/../routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($method, $uri);