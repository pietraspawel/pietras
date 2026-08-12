<?php

declare(strict_types=1);

namespace pietras\basic\Model;

class Route
{
    private string $controller;
    private array $params;

    public function __construct(string $controller, array $params = [])
    {
        $this->controller = $controller;
        $this->params = $params;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name): ?string
    {
        return $this->params[$name] ?? null;
    }
}
