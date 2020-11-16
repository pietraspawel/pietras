<?php

namespace pietras;

use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    protected $date1;

    protected function setUp(): void
    {
        $this->date1 = new Date("7.11.2020 12:45:14");
    }

    public function testToString()
    {
        $this->assertEquals("07.11.2020 12:45:14", $this->date1->__toString());
    }

    public function testToDBFormat()
    {
        $this->assertEquals("2020-11-07 12:45:14", $this->date1->toDBFormat());
    }

    public function testToMinute()
    {
        $this->assertEquals("45", $this->date1->getMinute());
    }
}
