<?php

namespace pietras\basic;

use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    private $application;

    protected function setUp(): void
    {
        $this->application = new Application("config/application.yaml");
    }

    public function testFindControllerByUrl()
    {
        $this->assertEquals("Controller1", Controller::findControllerByUrl($this->application, "url1"));
        $this->assertEquals("Controller2", Controller::findControllerByUrl($this->application, "url2"));
        $this->assertEquals("Controller2", Controller::findControllerByUrl($this->application, "url3"));
        $this->assertEquals(null, Controller::findControllerByUrl($this->application, "non_existed"));
    }
}
