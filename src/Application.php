<?php

namespace pietras\basic;

use pietras\basic\model\Config;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment as Twig;

/**
 * Store application properties and provide methods to control it.
 */
class Application
{
    /**
     * Array of filepaths to js scripts to run on the page.
     */
    private array $jsScripts;
    /**
     * Array of filepaths to css files.
     */
    private array $cssFiles;
    /**
     * Actual url.
     */
    private Url $url;
    /**
     * Controller object.
     */
    private ?Controller $controller;
    /**
     * Keeps configuration.
     */
    private Config $config;
    /**
     * Database handler.
     */
    private ?Database $database;
    /**
     * Tells if phpunit test is running.
     */
    private bool $isTest;
    /**
     * Pieprz do hasła.
     */
    private Renderer $renderer;
    private Router $router;

    public function __construct(Config $config, ?Database $database = null)
    {
        $this->isTest = false;
        if (defined('PHPUNIT_TESTING')) {
            $this->isTest = PHPUNIT_TESTING ?? false;
        }
        $this->config = $config;
        $this->jsScripts = [];
        $this->cssFiles = [];
        $this->url = new Url($this->config->get("app_url"));
        $this->router = new Router(Yaml::parseFile($this->config->get('routes_path')));
        $this->renderer = new Renderer($this->createTwig(), $this->config->get('translation_path'));
        $this->database = $database;
        $this->configureErrorHandling($this->getMode());
        $this->initSession();
    }

    private function configureErrorHandling($mode)
    {
        error_reporting(E_ALL);
        ini_set('log_errors', '1');
        if ($mode == "dev") {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
        } else {
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
        }
    }

    private function createTwig(): Twig
    {
        $debug = $this->getMode() === "dev" ? true : false;
        $loader = new \Twig\Loader\FilesystemLoader($this->config->get("templates_path"));
        $twig = new Twig($loader, [
            "cache" => $this->config->get("cache_path"),
            "debug" => $debug,
            "strict_variables" => true,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        return $twig;
    }

    private function initSession()
    {
        if (!$this->isTest()) {
            session_start();
        }
    }

    public function addCss(string $path): self
    {
        $path .= "?{$this->getCssVersion()}";
        if (!in_array($path, $this->getCssFiles())) {
            $this->cssFiles[] = $path;
        }
        return $this;
    }

    /**
     * Dodaje ścieżkę do pliku JavaScript do uruchomienia na stronie.
     */
    public function addJsScript(string $path): self
    {
        if (!in_array($path, $this->jsScripts)) {
            $this->jsScripts[] = $path;
        }
        return $this;
    }

    /**
     * Dodaje wszystkie pliki .js z folderu $path
     * do uruchomienia na stronie.
     */
    public function addJsFolder(string $folderPath): self
    {
        $paths = scandir($folderPath);
        foreach ($paths as $path) {
            if (substr($path, -strlen(".js")) == ".js") {
                $this->addJsScript("$folderPath/$path");
            }
        }
        return $this;
    }

    /**
     * Return actual version of CSS files.
     */
    public function getCssVersion(): string
    {
        return $this->config->get("CSS_VERSION");
    }

    public function getCssFiles(): array
    {
        return $this->cssFiles;
    }

    /**
     * Zawraca tablicę ze ścieżkami plików JavaScript do uruchomienia na stronie.
     */
    public function getJsScripts(): array
    {
        return $this->jsScripts;
    }

    /**
     * Return actual version of Java Script files.
     */
    public function getJSVersion(): string
    {
        return $this->config->get("JS_VERSION");
    }

    /**
     * Zwraca tryb aplikacji, dev|prod.
     */
    public function getMode(): string
    {
        return $this->config->get("MODE");
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    /**
     * Zwraca n-ty parametr URL lub null, gdy nieokreślony.
     * Index liczony od 1.
     */
    public function getUrlParam(int $index): ?string
    {
        return $this->url->getParam($index);
    }

    /**
     * Return application url without parameters.
     */
    public function getAppUrl(): string
    {
        return $this->url->getAppUrl();
    }

    /**
     * Return controller object.
     */
    public function getController(): ?\pietras\basic\Controller
    {
        return $this->controller;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getDatabase(): ?Database
    {
        return $this->database;
    }

    public function getPepper(): string
    {
        return $this->config->get("passwordPepper");
    }

    public function isTest(): bool
    {
        return $this->isTest;
    }

    /**
     * Set controller object.
     */
    public function setController(string $name): self
    {
        $name = ucfirst($name);
        $namespace = rtrim($this->config->get('controllersNamespace'), "\\");
        $controllerName = "{$namespace}\\{$name}";
        if (!class_exists($controllerName)) {
            throw new \RuntimeException("Controller {$controllerName} does not exist.");
        }
        if (!is_subclass_of($controllerName, Controller::class)) {
            throw new \RuntimeException(
                "{$controllerName} is not a controller."
            );
        }
            $this->controller = new $controllerName($this);
        return $this;
    }

    public function setJsScripts(array $value): self
    {
        $this->jsScripts = $value;
        return $this;
    }

    public function setCssFiles(array $value): self
    {
        $this->cssFiles = $value;
        return $this;
    }

    public function setUrl(Url $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function setTest(bool $value): self
    {
        $this->isTest = $value;
        return $this;
    }

    /**
     * Zmienia kontroler i uruchamia go.
     */
    public function runController(string $name): self
    {
        $this->setController($name);
        $this->getController()->handle();
        return $this;
    }

    public function getRenderer(): Renderer
    {
        return $this->renderer;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
