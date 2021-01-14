<?php

namespace pietras;

use PHPUnit\Framework\TestCase;

class ConfiguratorTest extends TestCase
{
    protected $conf1;

    protected function setUp(): void
    {
        $this->conf1 = new Configurator("konfiguracja1", __DIR__ . "/configuration1.json");
    }

    public function testLoadConfiguration()
    {
        $expected = [
            "wartość1" => 123,
            "wartość2" => "Zażółć gęślą jaźń"
        ];
        $this->assertEquals(
            $expected,
            $this->conf1->loadConfiguration("konfiguracja1", __DIR__ . "/configuration1.json")
        );
        $this->assertEquals($expected, $this->conf1->getConfiguration("konfiguracja1"));
    }

    public function testToGlobal()
    {
        $this->conf1->toGlobal("konfiguracja1");
        $this->assertTrue($GLOBALS["wartość1"] == 123);
        $this->assertTrue($GLOBALS["wartość2"] == "Zażółć gęślą jaźń");
    }
}
