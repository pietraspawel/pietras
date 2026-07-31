<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    private Renderer $renderer;

    protected function setUp(): void
    {
        $config = [
                "MODE" => "dev",
                "JS_VERSION" => "0.0.0",
                "CSS_VERSION" => "0.0.0",
                "app_url" => "http://pietraspawel.pl/testowisko",
                "templates_path" => "templates",
                "cache_path" => "cache",
                "translation_path" => "translation/pl",
                "routes_path" => "config/routes.yaml",
                "passwordPepper" => "pepper",
        ];
        $loader = new \Twig\Loader\FilesystemLoader($config["templates_path"]);
        $twig = new \Twig\Environment($loader, [
            "cache" => $config["cache_path"],
            "debug" => true,
            "strict_variables" => true,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        $this->renderer = new Renderer($twig, $config);
    }

    public function testGetTranslations()
    {
        $array = [
            "basetext" => [
                "menu" => [
                    "menu1" => "menu1",
                    "menu2" => "menu2",
                ]
            ],
            "text" => [
                "keyboard" => "klawiatura",
                "test" => "test",
                "notebook" => "zeszyt",
            ]
        ];
        $this->assertSame($array, $this->renderer->getTranslations("test.yaml"));
    }

    public function testRender()
    {
        $string = file_get_contents("templates/output.htm");
        $this->renderer->addJsScript("http://pietraspawel.pl/testowisko/js/any_js.js");
        $this->renderer->addNotice("Powiadomienie pierwsze");
        $this->renderer->addNotice("Notatka druga");
        $this->renderer->addNotice("Jakiś crap");
        $userArgs = [ "userArg1" => "aaa", "userArg2" => "bbb" ];
        $this->renderer->addRenderVar([ "renderVar1" => "rv1" ]);

        $this->assertSame($string, $this->renderer->render("test.twig", "test.yaml", $userArgs));
    }
}
