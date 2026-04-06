<?php
namespace App;

class RouterClass
{
    private array $routes = [];

    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                $controller = new $route['handler'][0]();
                $action = $route['handler'][1];
                $controller->$action();
                return;
            }
        }
        
        http_response_code(404);
        echo "404 Страница не найдена";
    }
}