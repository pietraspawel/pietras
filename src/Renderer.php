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
    private ?string $translationPath;
    private array $globalVars = [];

    public function __construct(Environment $twig, ?string $translationPath = null)
    {
        $this->twig = $twig;
        $this->translationPath = $translationPath;
    }

    /**
     * $templateFilename - ścieżka do pliku szablonu
     * $args - dodatkowe zmienne (opcja)
     * $translationFilename - ścieżka do pliku tłumaczeń (opcja)
     */
    public function render(string $templateFilename, array $args = [], ?string $translationFilename = null): string
    {
        $vars = array_merge($this->globalVars, $this->getTranslations($translationFilename), $args);
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

    private function getTranslations(?string $translationFilename): array
    {
        if ($this->translationPath === null) {
            return [];
        }
        $translation = [ "basetext" => Yaml::parseFile("{$this->translationPath}/base.yaml"), ];
        if ($translationFilename !== null) {
            $translation["text"] = Yaml::parseFile("{$this->translationPath}/{$translationFilename}");
        }

        return $translation;
    }
}
