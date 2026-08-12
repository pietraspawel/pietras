<?php

declare(strict_types=1);

namespace pietras\basic;

use pietras\basic\Model\Route;

/**
 * Dostarcza metody do routowania adresów.
 */
class Router
{
    private array $routesRules;

    public function __construct(array $routesRules)
    {
        $this->routesRules = $routesRules;
    }

    public function findRoute(string $path): ?Route
    {
        foreach ($this->routesRules as $controller => $paths) {
            foreach ($paths as $pattern) {
                $params = $this->matchRoute($pattern, $path);
                if ($params !== null) {
                    return new Route($controller, $params);
                }
            }
        }

        return null;
    }

    /**
     * Skrót do findRoute($path)->getController().
     * Jeśli nie ma kontrolera przypisanego do ścieżki to zwraca null.
     */
    public function findController(string $path): ?string
    {
        $route = $this->findRoute($path);
        if ($route === null) {
            return null;
        }
        return $route->getController();
    }

    // Porównuje wzorzec z konfiguracji z aktualną ścieżką.
    // Zwraca null jeśli nie pasuje.
    // Jeśli pasuje zwraca tablicę z parametrami (lub pustą, jeśli nie ma parametrów).
    public function matchRoute(string $pattern, string $currentPath): ?array
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $pathParts = explode('/', trim($currentPath, '/'));

        if (count($patternParts) !== count($pathParts)) {
            return null;
        }

        $params = [];

        foreach ($patternParts as $key => $part) {
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
