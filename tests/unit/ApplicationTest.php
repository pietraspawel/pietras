<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;
use pietras\basic\Model\Config;

class ApplicationTest extends TestCase
{
    private $app;
    private $config;

    protected function setUp(): void
    {
        $_SERVER["HTTP_HOST"] = "pietraspawel.pl";
        $_SERVER["REQUEST_URI"] = "/testowisko/param1/param2/param3?aaa=1&bbb=dupa";

        $config = Config::createFromYaml("config/application.yaml");
        $this->app = new \pietras\basic\Application($config, null);
        $this->config =
            [
                "MODE" => "dev",
                "JS_VERSION" => "0.0.0",
                "CSS_VERSION" => "0.0.0",
                "app_url" => "http://pietraspawel.pl/testowisko",
                "templates_path" => "templates",
                "cache_path" => "cache",
                "translation_path" => "translation/pl",
                "routes_path" => "config/routes.yaml",
                "passwordPepper" => "pepper",
                "controllersNamespace" => "pietras\\controller",
            ];
    }

    public function testContructor()
    {
        $this->assertInstanceOf(Router::class, $this->app->getRouter());
        $this->assertInstanceOf(Renderer::class, $this->app->getRenderer());
        $this->assertInstanceOf(Config::class, $this->app->getConfig());
        $this->assertSame(null, $this->app->getDatabase());
    }

    public function testConfig()
    {
        $this->assertSame("dev", $this->app->getConfig()->get('MODE'));
        $this->assertSame("cache", $this->app->getConfig()->get('cache_path'));
        $this->assertSame("pietras\\Controller", $this->app->getConfig()->get('controllersNamespace'));
    }

    public function testErrorReporting()
    {
        $this->assertSame(E_ALL, error_reporting());
        $this->assertSame('1', ini_get('log_errors'));
        $this->assertSame('1', ini_get('display_errors'));
        $this->assertSame('1', ini_get('display_startup_errors'));
    }

    public function testInitVariables()
    {
        $this->assertSame("dev", $this->app->getMode());
        $this->assertSame("0.0.0", $this->app->getJsVersion());
        $this->assertSame([], $this->app->getJsScripts());
        $this->assertSame("0.0.0", $this->app->getCssVersion());
        $this->assertSame([], $this->app->getCssFiles());
        $this->assertSame("http://pietraspawel.pl/testowisko", $this->app->getAppUrl());
        $this->assertSame("pepper", $this->app->getPepper());
    }

    public function testIsTest()
    {
        $this->assertTrue($this->app->isTest());
    }

    public function testAddCss()
    {
        $array = [
            "http://pietraspawel.pl/testowisko/css/inny_plik.css?0.0.0",
        ];
        $this->app->addCss("http://pietraspawel.pl/testowisko/css/inny_plik.css");
        $this->assertSame($array, $this->app->getCssFiles());
    }

    public function testAddJsFolder()
    {
        $array = [
            "js/empty1.js",
            "js/empty2.js",
        ];
        $this->assertEmpty($this->app->getJsScripts());
        $this->app->addJsFolder("js");
        $this->assertSame($array, $this->app->getJsScripts());
    }

    public function testSetController()
    {
        $controller = new \pietras\Controller\Test($this->app);
        $this->app->setController("test");
        $this->assertEquals($controller, $this->app->getController());
        $this->assertInstanceOf(\pietras\Controller\Test::class, $this->app->getController());
    }

    public function testSetNonExistingController()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            "Controller pietras\\Controller\\DoesNotExist does not exist."
        );
        $this->app->setController("DoesNotExist");
    }

    public function testSetWrongClassTypeController()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            "pietras\\Controller\\WrongClass is not a controller."
        );
        $this->app->setController("WrongClass");
    }

    public function testGetUrlParam()
    {
        $this->assertSame("param1", $this->app->getUrlParam(0));
        $this->assertSame("param2", $this->app->getUrlParam(1));
        $this->assertSame("param3", $this->app->getUrlParam(2));
    }

    public function testMonitoredVars()
    {
        $array = [
            "string" => "aaa",
            "int" => 123,
            "float" => 123.5,
            "bool" => true,
        ];
        $this->assertSame([], $this->app->getMonitoredVars());
        $this->app->addMonitoredVar("string", "aaa");
        $this->app->addMonitoredVar("int", 123);
        $this->app->addMonitoredVar("float", 123.5);
        $this->app->addMonitoredVar("bool", true);
        $this->assertSame(123, $this->app->getMonitoredVar("int"));
        $this->assertSame($array, $this->app->getMonitoredVars());
    }
}
