<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    private Renderer $renderer;

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
    }

    public function testStandardRender()
    {
        $string = file_get_contents("templates/output.htm");
        $this->renderer->addGlobalVar("globalVar1", "gv1");
        $userArgs = [
            "userArg1" => "userArg1",
            "userArg2" => 123,
        ];

        $this->assertSame($string, $this->renderer->render("test.twig", $userArgs, "test.yaml"));
    }
}
