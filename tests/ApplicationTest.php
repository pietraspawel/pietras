<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    private $app;
    private $config;

    protected function setUp(): void
    {
        $_SERVER["REQUEST_URI"] = "http://pietraspawel.pl/testowisko/param1/param2/param3?aaa=1&bbb=dupa";

        $this->app = new \pietras\basic\Application();
        $this->config =
            [
                "MODE" => "dev",
                "JS_VERSION" => "0.0.0",
                "CSS_VERSION" => "0.0.0",
                "base_url" => "http://pietraspawel.pl/testowisko",
                "templates_path" => "templates",
                "cache_path" => "cache",
                "translation_path" => "translation/pl",
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
        $this->assertEquals("http://pietraspawel.pl/testowisko", $this->app->getUrlBase());
        $this->assertEquals([], $this->app->getRenderVars());
    }

    public function testRenderVars()
    {
        $this->assertEquals([], $this->app->getRenderVars());
        $this->app->addRenderVar([ "aaa" => 111 ]);
        $this->assertEquals([ "aaa" => 111 ], $this->app->getRenderVars());
        $this->app->addRenderVar([ "bbb" => 222, "ccc" => 333, "aaa" => 11 ]);
        $this->assertEquals([ "aaa" => 11, "bbb" => 222, "ccc" => 333 ], $this->app->getRenderVars());
    }

    public function testGetTranslation()
    {
        $array = [
            "keyboard" => "klawiatura",
            "test" => "test",
            "notebook" => "zeszyt",
        ];
        $this->assertEquals($array, $this->app->getTranslation("test"));
    }
}
