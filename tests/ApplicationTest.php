<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testConfig()
    {
        $array = [ "aaa" => 11, "bbb" => "22" ];
        $app = new \pietras\basic\Application();
        $this->assertEquals($array, $app->getConfig());
    }
}
