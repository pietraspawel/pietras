<?php

declare(strict_types=1);

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $routes = [
            "Home" => ["/"],
            "Ajax" => ["/ajax"],
            "Canonical" => ["/canonical", "/canonical/{id}"],
            "HoneyPots" => ["/honey-pots", "/honey-pots/register"],
            "User" => ["/user", "/user/{id}"],
        ];
        $this->router = new Router($routes);
    }

    public function testFindRouteAndController()
    {
        $this->assertSame("Home", $this->router->findRoute("/")->getController());
        $this->assertSame("Ajax", $this->router->findRoute("/ajax")->getController());
        $this->assertSame("Canonical", $this->router->findRoute("/canonical")->getController());
        $this->assertSame("Canonical", $this->router->findRoute("/canonical/123")->getController());
        $this->assertSame("HoneyPots", $this->router->findRoute("/honey-pots")->getController());
        $this->assertSame("HoneyPots", $this->router->findRoute("/honey-pots/register")->getController());
        $this->assertNull($this->router->findRoute("/honey-pots/123"));
        $this->assertNull($this->router->findRoute("/nie-ma-takiej"));
    }

    public function testFindRouteAndParams()
    {
        $this->assertSame([], $this->router->findRoute("/")->getParams());
        $this->assertSame([], $this->router->findRoute("/ajax")->getParams());
        $this->assertSame([], $this->router->findRoute("/canonical")->getParams());
        $this->assertSame([ "id" => "123" ], $this->router->findRoute("/canonical/123")->getParams());
        $this->assertSame([], $this->router->findRoute("/honey-pots")->getParams());
        $this->assertSame([], $this->router->findRoute("/honey-pots/register")->getParams());
        $this->assertNull($this->router->findRoute("/honey-pots/123"));
    }

    public function testFindRouteAndParam()
    {
        $this->assertNull($this->router->findRoute("/")->getParam("id"));
        $this->assertSame("123", $this->router->findRoute("/canonical/123")->getParam("id"));
    }

    public function testFindController()
    {
        $this->assertSame("Home", $this->router->findController("/"));
        $this->assertSame("Ajax", $this->router->findController("/ajax"));
        $this->assertSame("Ajax", $this->router->findController("/ajax/"));
        $this->assertSame("Canonical", $this->router->findController("/canonical"));
        $this->assertSame("Canonical", $this->router->findController("/canonical/"));
        $this->assertSame("Canonical", $this->router->findController("/canonical/123"));
        $this->assertSame("HoneyPots", $this->router->findController("/honey-pots"));
        $this->assertSame("HoneyPots", $this->router->findController("/honey-pots/register"));
        $this->assertNull($this->router->findController("/honey-pots/123"));
        $this->assertNull($this->router->findController("/nie-ma-takiej"));
        $this->assertNull($this->router->findController("/user/123/edit"));
    }

    public function testMatchRoute()
    {
        $this->assertSame([], $this->router->matchRoute("/wzorzec", "/wzorzec"));
        $this->assertSame([], $this->router->matchRoute("/wzorzec", "/wzorzec/"));
        $this->assertSame(null, $this->router->matchRoute("/wzorzec", "/wzorzec/add"));
        $this->assertSame([ 'id' => '123' ], $this->router->matchRoute("/wzorzec/{id}/edit", "/wzorzec/123/edit"));
        $this->assertSame(null, $this->router->matchRoute("/wzorzec/{id}/edit", "/wzorzec/123"));
    }
}
