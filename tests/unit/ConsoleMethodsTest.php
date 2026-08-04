<?php

namespace pietras;

use PHPUnit\Framework\TestCase;
use pietras\ConsoleMethods as cm;

class ConsoleMethodsTest extends TestCase
{
    public function testConvertToType()
    {
        $this->assertEquals("zażółć gęślą jaźń", cm::convertToType('"zażółć gęślą jaźń"'));
        $this->assertIsString(cm::convertToType('"zażółć gęślą jaźń"'));
        $this->assertEquals(666, cm::convertToType('666'));
        $this->assertIsInt(cm::convertToType('666'));
        $this->assertEquals(-666.66, cm::convertToType('-666.66'));
        $this->assertIsFloat(cm::convertToType('-666.66'));
        $this->assertEquals(true, cm::convertToType('true'));
        $this->assertTrue(cm::convertToType('true'));
        $this->assertEquals(false, cm::convertToType('false'));
        $this->assertFalse(cm::convertToType('false'));
        $this->assertEquals(null, cm::convertToType('null'));
        $this->assertNull(cm::convertToType('null'));
        $this->assertEquals("null", cm::convertToType('"null"'));
        $this->assertIsString(cm::convertToType('"null"'));
    }
}
