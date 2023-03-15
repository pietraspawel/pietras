<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    private $app;
    private $config;

    protected function setUp(): void
    {
        $_SERVER["HTTP_HOST"] = "pietraspawel.pl";
        $_SERVER["REQUEST_URI"] = "/testowisko/param1/param2/param3?aaa=1&bbb=dupa";

        $this->app = new \pietras\basic\Application("config/application.yaml");
        $this->config =
            [
                "MODE" => "dev",
                "JS_VERSION" => "0.0.0",
                "CSS_VERSION" => "0.0.0",
                "base_url" => "http://pietraspawel.pl/testowisko",
                "templates_path" => "templates",
                "cache_path" => "cache",
                "translation_path" => "translation/pl",
                "routes_path" => "config/routes.yaml",
                "passwordPepper" => "pepper",
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
        $this->assertEquals("pepper", $this->app->getPepper());
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
        $this->assertEquals($array, $this->app->getTranslation("test.yaml"));
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
        $this->assertEquals($array, $this->app->getCssFiles());
    }

    public function testRender()
    {
        $string = file_get_contents("templates/output.htm");
        $this->app->addCss("http://pietraspawel.pl/testowisko/css/inny_plik.css");
        $this->app->addJsScript("http://pietraspawel.pl/testowisko/js/any_js.js");
        $this->app->addNotice("Powiadomienie pierwsze");
        $this->app->addNotice("Notatka druga");
        $this->app->addNotice("Jakiś crap");
        $userArgs = [ "userArg1" => "aaa", "userArg2" => "bbb" ];
        $this->app->addRenderVar([ "renderVar1" => "rv1" ]);

        $this->assertEquals($string, $this->app->render("test.twig", "test.yaml", $userArgs));
    }

    public function testAddJsFolder()
    {
        $array = [
            "js/empty1.js",
            "js/empty2.js",
        ];
        $this->assertEmpty($this->app->getJsScripts());
        $this->app->addJsFolder("js");
        $this->assertEquals($array, $this->app->getJsScripts());
    }

    public function testGetRoutes()
    {
        $array = [
            "Controller1" => [ "url1" ],
            "Controller2" => [
                "url2",
                "url3",
            ],
        ];
        $this->assertEquals($array, $this->app->getRoutes());
    }

    public function testSetController()
    {
        $controller = new \pietras\Controller\Test($this->app);
        $this->app->setController("test");
        $this->assertEquals($controller, $this->app->getController());
    }

    public function testGetUrlParam()
    {
        $this->assertEquals("param1", $this->app->getUrlParam(1));
        $this->assertEquals("param2", $this->app->getUrlParam(2));
        $this->assertEquals("param3", $this->app->getUrlParam(3));
    }
}
