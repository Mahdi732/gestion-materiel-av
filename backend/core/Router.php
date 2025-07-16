<?php

namespace Core;

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

        [$controller, $methodName] = $routes[$uri];

        if (!is_object($controller)) {
            http_response_code(500);
            echo json_encode(['error' => 'Expected object, got something else']);
            return;
        }

        if (!method_exists($controller, $methodName)) {
            http_response_code(404);
            echo json_encode(['error' => 'Method not found']);
            return;
        }

        $controller->$methodName();
        }
}