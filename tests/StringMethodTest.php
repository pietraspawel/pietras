<?php

namespace pietras;

use PHPUnit\Framework\TestCase;
use pietras\StringMethods as sm;

class StringTest extends TestCase
{
    public function testPlToLatin()
    {
        $this->assertEquals("Zazolc gesla jazn.", StringMethods::plToLatin("Zażółć gęślą jaźń."));
        $this->assertEquals(
            "W Szczebrzeszynie chrzaszcz brzmi w trzcinie.",
            StringMethods::plToLatin("W Szczebrzeszynie chrząszcz brzmi w trzcinie.")
        );
        $this->assertEquals("DUZE LITERKI LACINSKIE.", StringMethods::plToLatin("DUŻĘ ŁITĘRKI ŁĄĆIŃŚKIĘ."));
    }

    public function testPlToLatinLowercase()
    {
        $this->assertEquals(
            "zazolc gesla jazn.",
            StringMethods::plToLatin("Zażółć gęślą jaźń.", StringMethods::LOWERCASE)
        );
        $this->assertEquals(
            "w szczebrzeszynie chrzaszcz brzmi w trzcinie.",
            StringMethods::plToLatin("W Szczebrzeszynie chrząszcz brzmi w trzcinie.", StringMethods::LOWERCASE)
        );
        $this->assertEquals(
            "duze literki lacinskie.",
            StringMethods::plToLatin("DUŻĘ ŁITĘRKI ŁĄĆIŃŚKIĘ.", StringMethods::LOWERCASE)
        );
    }

    public function testCompareText()
    {
        $this->assertEquals(
            100,
            StringMethods::compareText(
                "W Szczebrzeszynie chrząszcz brzmi w trzcinie",
                "W Szczebrzeszynie chrząszcz brzmi w trzcinie"
            )
        );
        $this->assertEquals(
            100,
            StringMethods::compareText(
                "W Szczebrzeszynie chrząszcz brzmi w trzcinie",
                "WSzczebrzeszyniechrząszczbrzmiwtrzcinie"
            )
        );
        $this->assertEquals(
            100,
            StringMethods::compareText(
                "W Szczebrzeszynie chrząszcz brzmi w trzcinie",
                "w szczebrzeszynie chrząszcz brzmi w trzcinie"
            )
        );
        $this->assertEquals(
            100,
            StringMethods::compareText(
                "W Szczebrzeszynie chrząszcz brzmi w trzcinie",
                "W Szczebrzeszynie chrzaszcz brzmi w trzcinie"
            )
        );
        $this->assertEquals(
            100,
            StringMethods::compareText(
                "W Szczebrzeszynie chrząszcz brzmi w trzcinie",
                "wszczebrzeszynie chrzaszczbrzmi wtrzcinie"
            )
        );
        $this->assertEquals(
            21.05263157894737,
            StringMethods::compareText(
                "W Szczebrzeszynie chrząszcz brzmi w trzcinie",
                "Litwo, Ojczyzno moja"
            )
        );
    }

    public function testCamelToSnake()
    {
        $this->assertEquals("camel_case", sm::camelToSnake("camelCase"));
    }
}
