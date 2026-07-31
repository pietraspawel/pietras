<?php

namespace pietras\basic\model;

use Symfony\Component\Yaml\Yaml;

/**
 * Stores application configuration.
 */
class Config
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function createFromYaml(string $file): self
    {
        if (!is_file($file)) {
            throw new \RuntimeException("Configuration file '{$file}' does not exist.");
        }

        $data = Yaml::parseFile($file);

        if (!is_array($data)) {
            throw new \RuntimeException("Configuration file '{$file}' is invalid.");
        }

        return new self($data);
    }

    public function get(string $key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new \InvalidArgumentException(
                "Configuration key '{$key}' does not exist."
            );
        }

        return $this->data[$key];
    }
}
