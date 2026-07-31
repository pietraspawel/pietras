<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    private Renderer $renderer;
    private Renderer $rendererWithoutTranslations;

    protected function setUp(): void
    {
        $loader = new \Twig\Loader\FilesystemLoader("templates");
        $twig = new \Twig\Environment($loader, [
            "cache" => false,
            "debug" => true,
            "strict_variables" => true,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $this->renderer = new Renderer($twig, "translation/pl");
        $this->rendererWithoutTranslations = new Renderer($twig);
    }

    public function testStandardRender()
    {
        $string = file_get_contents("templates/output.htm");
        $this->renderer->addGlobalVar("globalVar1", "gv1");
        $this->renderer->addGlobalVars([
            "globalVar2" => "gv2",
            "title" => "Tytuł pierowtny",
        ]);
        $userArgs = [
            "userArg1" => "userArg1",
            "userArg2" => 123,
            "title" => "Tytuł zmieniony",
        ];

        $this->assertSame($string, $this->renderer->render("test.twig", $userArgs, "test.yaml"));
    }

    public function testRenderWithoutTranslations()
    {
        $output = "Jakieś zdanie po polsku.\nglobalVar1 = gv1\nuserArg1 = userArg1\nuserArg2 = 123";
        $this->rendererWithoutTranslations->addGlobalVar("globalVar1", "gv1");
        $userArgs = [
            "userArg1" => "userArg1",
            "userArg2" => 123,
        ];

        $this->assertSame($output, $this->rendererWithoutTranslations->render("test2.twig", $userArgs, "test.yaml"));
    }
}
