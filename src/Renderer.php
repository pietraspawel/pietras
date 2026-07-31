<?php

namespace pietras\basic;

use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * Renders templates using Twig.
 */
class Renderer
{
    private Environment $twig;
    private array $config;
    private array $globalVars = [];

    public function __construct(Environment $twig, array $config)
    {
        $this->twig = $twig;
        $this->config = $config;
    }

    /**
     * $templateFilename - ścieżka do pliku szablonu
     * $args - dodatkowe zmienne (opcja)
     * $translationFilename - ścieżka do pliku tłumaczeń (opcja)
     */
    public function render(string $templateFilename, ?string $translationFilename = null): string
    {
        $vars = array_merge($this->globalVars, $this->getTranslations($translationFilename));
        return $this->twig->render($templateFilename, $vars);
    }

    public function addGlobalVar(string $name, $value): self
    {
        $this->globalVars[$name] = $value;

        return $this;
    }

    public function addGlobalVars(array $vars): self
    {
        foreach ($vars as $name => $value) {
            $this->addGlobalVar($name, $value);
        }

        return $this;
    }

    public function getTranslations(?string $translationFilename): array
    {
        $path = $this->config["translation_path"];

        $translation = [
            "basetext" => Yaml::parseFile("{$path}/base.yaml"),
        ];

        if ($translationFilename !== null) {
            $translation["text"] = Yaml::parseFile(
                "{$path}/{$translationFilename}"
            );
        }

        return $translation;
    }
}
