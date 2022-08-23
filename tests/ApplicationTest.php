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
        $this->config = [ "MODE" => "dev", "bbb" => "22" ];
    }

    public function testConfig()
    {
        $this->assertEquals($this->config, $this->app->getConfig());
    }

    public function testErrorReporting()
    {
        $this->assertEquals(E_ALL, error_reporting());
    }
}
