<?php

namespace pietras\basic;

use pietras\basic\model\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $data = [
            "string" => "value1",
            "int" => 123,
            "float" => 123.45,
            "bool" => true,
        ];

        $this->config = new Config($data);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Config::class, $this->config);
        $this->assertSame("value1", $this->config->get("string"));
        $this->assertSame(123, $this->config->get("int"));
        $this->assertSame(123.45, $this->config->get("float"));
        $this->assertSame(true, $this->config->get("bool"));
    }

    public function testNonExistingKeys()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Configuration key 'fakeKey' does not exist."
        );
        $this->config->get('fakeKey');
    }

    public function testCreateFromYaml()
    {
        $config = Config::createFromYaml('config/application.yaml');
        $this->assertInstanceOf(Config::class, $config);
        $this->assertSame("dev", $config->get("MODE"));
        $this->assertSame("0.0.0", $config->get("CSS_VERSION"));
        $this->assertSame("pepper", $config->get("passwordPepper"));
    }

    public function testCreateFromYamlFileDoesNotExist()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            "Configuration file 'config/not-found.yaml' does not exist."
        );

        Config::createFromYaml("config/not-found.yaml");
    }

    public function testCreateFromYamlYamlIsNotArray()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            "Configuration file 'config/invalid_application.yaml' is invalid."
        );

        Config::createFromYaml("config/invalid_application.yaml");
    }
}
