<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    private Url $url;
    private Url $url2;
    private Url $url3;
    private Url $url4;

    protected function setUp(): void
    {
        $_SERVER["REQUEST_URI"] = "/testowo/param1/param2/param3?aaa=1&bbb=dupa";
        $this->url = new \pietras\basic\Url("http://pietraspawel.pl/testowo");

        $_SERVER["REQUEST_URI"] = "/param1/param2/param3?aaa=1&bbb=dupa";
        $this->url2 = new \pietras\basic\Url("http://pietraspawel.pl");

        $_SERVER["REQUEST_URI"] = "/testowo";
        $this->url3 = new \pietras\basic\Url("http://pietraspawel.pl/testowo");

        $_SERVER["REQUEST_URI"] = "/project-old/test";
        $this->url4 = new \pietras\basic\Url("http://pietraspawel.pl/project");
    }

    public function testAppUrl()
    {
        $this->assertEquals("http://pietraspawel.pl/testowo", $this->url->getAppUrl());
        $this->assertEquals("http://pietraspawel.pl", $this->url2->getAppUrl());
        $this->assertEquals("http://pietraspawel.pl/testowo", $this->url3->getAppUrl());
        $this->assertEquals("http://pietraspawel.pl/project", $this->url4->getAppUrl());
    }

    public function testBaseUrl()
    {
        $this->assertEquals("/testowo", $this->url->getBaseUrl());
        $this->assertEquals("", $this->url2->getBaseUrl());
        $this->assertEquals("/testowo", $this->url3->getBaseUrl());
        $this->assertEquals("/project", $this->url4->getBaseUrl());
    }

    public function testFullUrl()
    {
        $this->assertEquals(
            "http://pietraspawel.pl/testowo/param1/param2/param3?aaa=1&bbb=dupa",
            $this->url->getFullUrl()
        );
        $this->assertEquals(
            "http://pietraspawel.pl/param1/param2/param3?aaa=1&bbb=dupa",
            $this->url2->getFullUrl()
        );
        $this->assertEquals("http://pietraspawel.pl/testowo", $this->url3->getFullUrl());
        $this->assertEquals("http://pietraspawel.pl/project/project-old/test", $this->url4->getFullUrl());
    }

    public function testUri()
    {
        $this->assertEquals("/param1/param2/param3?aaa=1&bbb=dupa", $this->url->getUri());
        $this->assertEquals("/param1/param2/param3?aaa=1&bbb=dupa", $this->url2->getUri());
        $this->assertEquals("/", $this->url3->getUri());
        $this->assertEquals("/project-old/test", $this->url4->getUri());
    }

    public function testPath()
    {
        $this->assertEquals("/param1/param2/param3", $this->url->getPath());
        $this->assertEquals("/param1/param2/param3", $this->url2->getPath());
        $this->assertEquals("/", $this->url3->getPath());
        $this->assertEquals("/project-old/test", $this->url4->getPath());
    }

    public function testParams()
    {
        $array = [ "param1", "param2", "param3" ];
        $this->assertEquals($array, $this->url->getParams());
        $this->assertEquals("param1", $this->url->getParam(0));
        $this->assertEquals("param2", $this->url->getParam(1));
        $this->assertEquals("param3", $this->url->getParam(2));
        $this->assertEquals(3, $this->url->countParams());
        $this->assertEquals($array, $this->url2->getParams());
        $this->assertEquals("param1", $this->url2->getParam(0));
        $this->assertEquals("param2", $this->url2->getParam(1));
        $this->assertEquals("param3", $this->url2->getParam(2));
        $this->assertEquals(3, $this->url2->countParams());
        $this->assertEquals([], $this->url3->getParams());
        $this->assertEquals(null, $this->url3->getParam(0));
        $this->assertEquals(null, $this->url3->getParam(1));
        $this->assertEquals(null, $this->url3->getParam(2));
        $this->assertEquals(0, $this->url3->countParams());
        $this->assertEquals(["project-old", "test"], $this->url4->getParams());
        $this->assertEquals("project-old", $this->url4->getParam(0));
        $this->assertEquals("test", $this->url4->getParam(1));
        $this->assertEquals(null, $this->url4->getParam(2));
        $this->assertEquals(2, $this->url4->countParams());
    }
}
