<?php

namespace pietras\basic\model;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public static function createFromYaml(string $file): self
    {
        return new self(Yaml::parseFile($file));
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
