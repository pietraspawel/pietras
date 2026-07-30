<?php

namespace pietras\basic;

use pietras\basic\model\Route;

/**
 * Dostarcza metody do routowania adresów.
 */
class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function findRoute(string $path): ?Route
    {
        foreach ($this->routes as $controller => $paths) {
            foreach ($paths as $route) {
                $params = $this->matchRoute($route, $path);
                if ($params !== null) {
                    return new Route($controller, $params);
                }
            }
        }

        return null;
    }

    // Porównuje routę z konfiguracji z url path.
    // Zwraca null jeśli nie pasuje.
    // Jeśli pasuje zwraca tablicę z parametrami (lub pustą, jeśli nie ma parametrów).
    private static function matchRoute(string $route, string $currentPath): ?array
    {
        $routeParts = explode('/', trim($route, '/'));
        $pathParts = explode('/', trim($currentPath, '/'));

        if (count($routeParts) !== count($pathParts)) {
            return null;
        }

        $params = [];

        foreach ($routeParts as $key => $part) {
            if (preg_match('/^\{(.+)\}$/', $part, $matches)) {
                $params[$matches[1]] = $pathParts[$key];
                continue;
            }

            if ($part !== $pathParts[$key]) {
                return null;
            }
        }

        return $params;
    }
}
