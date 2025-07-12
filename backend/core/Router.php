<?php

use Core;

class Router {
    private static array $routes = [];

    public static function get (string $uri, array $method) : void {
        self::$routes['GET'][$uri] = $method;
    }

    public static function post (string $uri, array $method) : void {
        self::$routes['POST'][$uri] = $method;
    }

    public static function resolve (string $method, string $uri) : void {
        $uri = rtrim($uri, '/');

        $routes = self::$routes[$method] ?? [];

        if (!isset($routes[$uri])) {
            http_response_code(404);
            echo json_encode(['error' => 'route not found']);
            return;
        }

        [$ControllerName, $methodName] = $routes[$uri];

        if (!isset($ControllerName)) {
            http_response_code(404);
            echo json_encode(['error' => 'Constroller not found']);
            return;
        }

        $controller = new $ControllerName ();

        if (!method_exists($controller, $methodName)) {
            http_response_code(404);
            echo json_encode(['error' => 'Method not found !']);
            return;
        }

        $controller->$methodName();
    }
}