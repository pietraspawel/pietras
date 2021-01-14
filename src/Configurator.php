<?php

namespace pietras;

class Configurator
{
    private $configuration;

    public function __construct(string $index, string $filename)
    {
        $this->configuration[$index] = $this->loadConfiguration($index, $filename);
    }

    /**
    * Funkcja ładuje konfigurację z pliku $filename do konfiguracji o nazwie $index.
    */
    public function loadConfiguration(string $index, string $filename): array
    {
        $conf = json_decode(file_get_contents($filename), true);
        $this->configuration[$index] = $conf;
        return $conf;
    }

    /**
    * Zwraca konfigurację o nazwie $index.
    */
    public function getConfiguration(string $index): array
    {
        return $this->configuration[$index];
    }

    /**
    * Konfigurację o nazwie $index przypisuje do zmiennych globalnych o nazwach takich jak ich klucze.
    */
    public function toGlobal(string $index): void
    {
        foreach ($this->configuration[$index] as $key => $value) {
            $GLOBALS[$key] = $value;
        }
    }
}
