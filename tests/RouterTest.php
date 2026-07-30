<?php

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

    public function testFindController()
    {
        $this->assertEquals("Home", $this->router->findRoute("/")->getController());
        $this->assertEquals("Ajax", $this->router->findRoute("/ajax")->getController());
        $this->assertEquals("Canonical", $this->router->findRoute("/canonical")->getController());
        $this->assertEquals("Canonical", $this->router->findRoute("/canonical/123")->getController());
        $this->assertEquals("HoneyPots", $this->router->findRoute("/honey-pots")->getController());
        $this->assertEquals("HoneyPots", $this->router->findRoute("/honey-pots/register")->getController());
        $this->assertEquals(null, $this->router->findRoute("/honey-pots/123"));
        $this->assertEquals(null, $this->router->findRoute("/nie-ma-takiej"));
    }

    public function testFindParams()
    {
        $this->assertEquals([], $this->router->findRoute("/")->getParams());
        $this->assertEquals([], $this->router->findRoute("/ajax")->getParams());
        $this->assertEquals([], $this->router->findRoute("/canonical")->getParams());
        $this->assertEquals([ "id" => 123 ], $this->router->findRoute("/canonical/123")->getParams());
        $this->assertEquals([], $this->router->findRoute("/honey-pots")->getParams());
        $this->assertEquals([], $this->router->findRoute("/honey-pots/register")->getParams());
        $this->assertEquals(null, $this->router->findRoute("/honey-pots/123"));
    }

    public function testFindParam()
    {
        $this->assertEquals(null, $this->router->findRoute("/")->getParam("id"));
        $this->assertEquals(123, $this->router->findRoute("/canonical/123")->getParam("id"));
    }
}
