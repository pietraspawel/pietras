<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    private $url;

    protected function setUp(): void
    {
        $_SERVER["HTTP_HOST"] = "pietraspawel.pl";
        $_SERVER["REQUEST_URI"] = "/testowo/param1/param2/param3?aaa=1&bbb=dupa";
        $this->url = new \pietras\basic\Url("http://pietraspawel.pl/testowo");
    }

    public function testUrl()
    {
        $this->assertEquals(
            "http://pietraspawel.pl/testowo/param1/param2/param3?aaa=1&bbb=dupa",
            $this->url->getFullUrl()
        );
    }

    public function testBasicUrl()
    {
        $this->assertEquals("http://pietraspawel.pl/testowo", $this->url->getBaseUrl());
    }

    public function testParams()
    {
        $this->assertEquals("param1", $this->url->getParam(1));
        $this->assertEquals("param2", $this->url->getParam(2));
        $this->assertEquals("param3", $this->url->getParam(3));
    }
}
