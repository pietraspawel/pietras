<?php

namespace pietras\basic;

use Symfony\Component\Yaml\Yaml;

/**
 * Store application properties and provide methods to control it.
 */
class Application
{
    /**
     * Must be "dev" or "prod".
     */
    private string $mode;
    /**
     * Actual version of Java Script files.
     */
    private string $jsVersion;
    /**
     * Array of filepaths to js scripts to run on the page.
     */
    private array $jsScripts;
    /**
     * Actual version of CSS files.
     */
    private string $cssVersion;
    /**
     * Array of filepaths to css files.
     */
    private array $cssFiles;
    /**
     * List of errors;
     */
    private array $errors;
    /**
     * List of notices.
     */
    private array $notices;
    /**
     * Actual url.
     */
    private Url $url;
    /**
     * Controller object.
     */
    private Controller $controller;
    /**
     * Keeps configuration.
     */
    private array $config;
    /**
     * Obiekt Twiga.
     */
    private \Twig\Environment $twig;
    /**
     * Database handler.
     */
    private Database $database;
    /**
     * Tells if phpunit test is running.
     */
    private bool $isTest;
    /**
     * Pieprz do hasła.
     */
    private string $pepper;
    private Router $router;

    /**
     * $configFilepath
     *  ścieżka do pliku konfiguracji
    */
    public function __construct(
        string $configFilepath = "../config/application.yaml",
        string $dbConfigFilepath = "../config/database.yaml"
    ) {
        $this->isTest = false;
        if (defined('PHPUNIT_TESTING')) {
            $this->isTest = PHPUNIT_TESTING ?? false;
        }
        $this->setConfig(Yaml::parseFile($configFilepath));
        $this->__setErrorReporting($this->getMode());
        $this->__initVariables();
        $this->__initTwig();
        if (file_exists($dbConfigFilepath)) {
            $this->__initDatabase($dbConfigFilepath);
        }
        $this->__initSession();

        $this->router = new Router(Yaml::parseFile($this->config['routes_path']));
    }

    protected function __setErrorReporting($mode)
    {
        if ($mode == "dev") {
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
    }

    protected function __initVariables()
    {
        $config = $this->getConfig();
        $this
            ->setJsVersion($config["JS_VERSION"])
            ->setJsScripts([])
            ->setCssVersion($config["CSS_VERSION"])
            ->setCssFiles([])
            ->setErrors([])
            ->setNotices([])
            ->setPepper($config["passwordPepper"])
            ->setUrl(new Url($config["app_url"]));
    }

    protected function __initTwig()
    {
        $config = $this->getConfig();
        $debug = $this->getMode() === "dev" ? true : false;
        $loader = new \Twig\Loader\FilesystemLoader($config["templates_path"]);
        $this->twig = new \Twig\Environment($loader, [
            "cache" => $config["cache_path"],
            "debug" => $debug,
            "strict_variables" => true,
        ]);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
    }

    protected function __initDatabase(string $dbConfigFilepath)
    {
        $dbConfig = (Yaml::parseFile($dbConfigFilepath));
        $host = $dbConfig["DB_HOST"];
        $user = $dbConfig["DB_USER"];
        $pass = $dbConfig["DB_PASS"];
        $databaseName = $dbConfig["DB_NAME"];
        $this->database = new Database($host, $user, $pass, $databaseName);
        if ($this->database->connect_error !== null) {
            if ($this->getMode() == "dev") {
                trigger_error("Database connection error: " . $this->database->connect_error, E_USER_ERROR);
            } else {
                echo "Database connection error.";
            }
            die();
        }
    }

    protected function __initSession()
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
     * Add error to errors list.
     *
     * @param string $error Error description.
     */
    public function addError(string $error): self
    {
        $this->errors[] = $error;
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
     * Add notice to notices list.
     *
     * @param string $notice Notice description.
     */
    public function addNotice(string $notice): self
    {
        $this->notices[] = $notice;
        return $this;
    }

    /**
     * Return actual version of CSS files.
     *
     * @return string
     */
    public function getCssVersion(): string
    {
        return $this->cssVersion;
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
     *
     * @return string
     */
    public function getJSVersion(): string
    {
        return $this->getConfig()["JS_VERSION"];
    }

    /**
     * Return list of errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Zwraca tryb aplikacji, dev|prod.
     */
    public function getMode(): string
    {
        return $this->config["MODE"];
    }

    /**
     * Return list of notices.
     *
     * @return array
     */
    public function getNotices(): array
    {
        return $this->notices;
    }

    public function getTwig(): \Twig\Environment
    {
        return $this->twig;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    /**
     * Zwraca n-ty parametr URL lub null, gdy nieokreślony.
     * Index liczony od 1.
     *
     * @return null|string
     */
    public function getUrlParam(int $index): ?string
    {
        return $this->url->getParam($index);
    }

    /**
     * Return application url without parameters.
     *
     * @return string
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

    /**
     * Zwraca konfigurację w formie tablicy.
     */
    public function getConfig(): ?array
    {
        return $this->config;
    }

    public function getDatabase(): ?Database
    {
        return $this->database;
    }

    public function getPepper(): string
    {
        return $this->pepper;
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
        $controllerName = "pietras\\Controller\\{$name}";
        $this->controller = new $controllerName($this);
        return $this;
    }

    public function setConfig(array $value): self
    {
        $this->config = $value;
        return $this;
    }

    public function setJsVersion(string $value): self
    {
        $this->jsVersion = $value;
        return $this;
    }

    public function setJsScripts(array $value): self
    {
        $this->jsScripts = $value;
        return $this;
    }

    public function setCssVersion(string $value): self
    {
        $this->cssVersion = $value;
        return $this;
    }

    public function setCssFiles(array $value): self
    {
        $this->cssFiles = $value;
        return $this;
    }

    public function setErrors(array $value): self
    {
        $this->errors = $value;
        return $this;
    }

    public function setNotices(array $value): self
    {
        $this->notices = $value;
        return $this;
    }

    public function setUrl(Url $url): self
    {
        $this->url = $url;
        return $this;
    }

    private function setPepper(string $pepper): self
    {
        $this->pepper = $pepper;
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

    public function getRouter(): Router
    {
        return $this->router;
    }
}
