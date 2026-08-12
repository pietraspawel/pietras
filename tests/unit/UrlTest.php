<?php

declare(strict_types=1);

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
        $this->assertSame("http://pietraspawel.pl/testowo", $this->url->getAppUrl());
        $this->assertSame("http://pietraspawel.pl", $this->url2->getAppUrl());
        $this->assertSame("http://pietraspawel.pl/testowo", $this->url3->getAppUrl());
        $this->assertSame("http://pietraspawel.pl/project", $this->url4->getAppUrl());
    }

    public function testBaseUrl()
    {
        $this->assertSame("/testowo", $this->url->getBaseUrl());
        $this->assertSame("", $this->url2->getBaseUrl());
        $this->assertSame("/testowo", $this->url3->getBaseUrl());
        $this->assertSame("/project", $this->url4->getBaseUrl());
    }

    public function testFullUrl()
    {
        $this->assertSame(
            "http://pietraspawel.pl/testowo/param1/param2/param3?aaa=1&bbb=dupa",
            $this->url->getFullUrl()
        );
        $this->assertSame(
            "http://pietraspawel.pl/param1/param2/param3?aaa=1&bbb=dupa",
            $this->url2->getFullUrl()
        );
        $this->assertSame("http://pietraspawel.pl/testowo", $this->url3->getFullUrl());
        $this->assertSame("http://pietraspawel.pl/project/project-old/test", $this->url4->getFullUrl());
    }

    public function testUri()
    {
        $this->assertSame("/param1/param2/param3?aaa=1&bbb=dupa", $this->url->getUri());
        $this->assertSame("/param1/param2/param3?aaa=1&bbb=dupa", $this->url2->getUri());
        $this->assertSame("/", $this->url3->getUri());
        $this->assertSame("/project-old/test", $this->url4->getUri());
    }

    public function testPath()
    {
        $this->assertSame("/param1/param2/param3", $this->url->getPath());
        $this->assertSame("/param1/param2/param3", $this->url2->getPath());
        $this->assertSame("/", $this->url3->getPath());
        $this->assertSame("/project-old/test", $this->url4->getPath());
    }

    public function testParams()
    {
        $array = [ "param1", "param2", "param3" ];
        $this->assertSame($array, $this->url->getParams());
        $this->assertSame("param1", $this->url->getParam(0));
        $this->assertSame("param2", $this->url->getParam(1));
        $this->assertSame("param3", $this->url->getParam(2));
        $this->assertSame(3, $this->url->countParams());
        $this->assertSame($array, $this->url2->getParams());
        $this->assertSame("param1", $this->url2->getParam(0));
        $this->assertSame("param2", $this->url2->getParam(1));
        $this->assertSame("param3", $this->url2->getParam(2));
        $this->assertSame(3, $this->url2->countParams());
        $this->assertSame([], $this->url3->getParams());
        $this->assertSame(null, $this->url3->getParam(0));
        $this->assertSame(null, $this->url3->getParam(1));
        $this->assertSame(null, $this->url3->getParam(2));
        $this->assertSame(0, $this->url3->countParams());
        $this->assertSame(["project-old", "test"], $this->url4->getParams());
        $this->assertSame("project-old", $this->url4->getParam(0));
        $this->assertSame("test", $this->url4->getParam(1));
        $this->assertSame(null, $this->url4->getParam(2));
        $this->assertSame(2, $this->url4->countParams());
    }

    public function testParamEquals()
    {
        $this->assertTrue($this->url->paramNEquals(0, "param1"));
        $this->assertTrue($this->url->paramNEquals(1, "param2"));
        $this->assertTrue($this->url->paramNEquals(2, "param3"));
        $this->assertFalse($this->url->paramNEquals(0, "param3"));
        $this->assertFalse($this->url->paramNEquals(3, "param3"));
    }
}
