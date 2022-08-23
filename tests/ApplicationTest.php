<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    private $app;
    private $config;

    protected function setUp(): void
    {
        $this->app = new \pietras\basic\Application();
        $this->config =
            [
                "MODE" => "dev",
                "JS_VERSION" => "0.0.0",
                "CSS_VERSION" => "0.0.0",
            ];
    }

    public function testConfig()
    {
        $this->assertEquals($this->config, $this->app->getConfig());
    }

    public function testErrorReporting()
    {
        $this->assertEquals(E_ALL, error_reporting());
    }

    public function testInitVariables()
    {
        $this->assertEquals("dev", $this->app->getMode());
        $this->assertEquals("0.0.0", $this->app->getJsVersion());
        $this->assertEquals([], $this->app->getJsScripts());
        $this->assertEquals("0.0.0", $this->app->getCssVersion());
        $this->assertEquals([], $this->app->getCssFiles());
        $this->assertEquals([], $this->app->getErrors());
        $this->assertEquals([], $this->app->getNotices());
        // $this->assertEquals("http://pietraspawel.pl/testowisko", $this->app->getUrlBase());
        // $this->assertEquals([ "url1" => "url1", "url2" => "url2" ], $this->app->getUrls());
        // $this->assertEquals([], $this->app->getRenderVars());
    }
}
