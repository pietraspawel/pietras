<?php

namespace pietras\basic;

use Symfony\Component\Yaml\Yaml;

/**
 * Store application properties and provide methods to control it.
 */
class Application
{
    /**
     * @var string $mode Must be "dev" or "prod".
     */
    private $mode;
    /**
     * @var string $jsVersion Actual version of Java Script files.
     */
    private $jsVersion;
    /**
     * @var array $jsScripts Array of filepaths to js scripts to run on the page.
     */
    private $jsScripts;
    /**
     * @var string $cssVersion Actual version of CSS files.
     */
    private $cssVersion;
    /**
     * @var array $cssFiles Array of filepaths to css files.
     */
    private $cssFiles;
    /**
     * @var array List of errors;
     */
    private $errors;
    /**
     * @var array List of notices.
     */
    private $notices;
    /**
     * @var Url $url Actual url.
     */
    private $url;
    /**
     * @var Controller $contoller Controller object.
     */
    private $controller;
    /**
     * @var array $config Keeps configuration.
     */
    private $config;
    /**
     * @var \Twig\Environment $twig Obiekt Twiga.
     */
    private $twig;
    /**
     * Global variables, which are sended to every twig template.
     * @var array $globalRenderVars
     */
    private $renderVars;
    /**
     * @var Database $database Database handler.
     */
    private $database;

    public function __construct()
    {
        $this->setConfig(Yaml::parseFile("config/application.yaml"));
        $this->__setErrorReporting($this->getMode());
        $this->__initVariables();
        $this->__initTwig();
        $this->__initDatabase();
        $this->__initSession();
    }

    private function __setErrorReporting($mode)
    {
        if ($mode == "dev") {
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
    }

    private function __initVariables()
    {
        $config = $this->getConfig();
        $this
            ->setJsVersion($config["JS_VERSION"])
            ->setJsScripts([])
            ->setCssVersion($config["CSS_VERSION"])
            ->setCssFiles([])
            ->setErrors([])
            ->setNotices([])
            ->setUrl(new Url($config["base_url"]));
        $this->renderVars = [];
    }

    private function __initTwig()
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

    private function __initDatabase()
    {
        $dbConfig = (Yaml::parseFile("config/database.yaml"));
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

    private function __initSession()
    {
        // Check first if headers sent,
        // otherwise it print error in unit tests.
        if (!headers_sent()) {
            session_start();
        }
    }

    /**
     * Zwraca tablicę z tłumaczeniem zapisanym w $translationFilename.
     */
    public function getTranslation(string $translationFilename): ?array
    {
        $path = $this->getConfig()["translation_path"];
        return Yaml::parseFile("{$path}/{$translationFilename}.yaml");
    }

    /**
     * Funkcja renderująca widok przy pomocy Twiga.
     * Przekazuje zmienne $args, jak i globalne zmienne z tego obiektu.
     */
    public function render(string $templateFilename, string $translationFilename = null, ?array $userArgs = []): string
    {
        $globalArgs = [
            "urlBase" => $this->urlBase,
            "urlCss" => [
                "{$this->urlBase}css/bootstrap.min.css?{$this->cssVersion}",
                "{$this->urlBase}css/style.css?{$this->cssVersion}",
            ],
            "urlJs" => [],
            "errors" => [],
            "notices" => [],
        ];
        foreach ($this->jsScripts as $value) {
            $globalArgs["urlJs"][] = "$value?" . $this->jsVersion;
        }
        foreach ($this->errors as $value) {
            $globalArgs["errors"][] = $value;
        }
        foreach ($this->notices as $value) {
            $globalArgs["notices"][] = $value;
        }
        $config = json_decode(file_get_contents(__DIR__  . "/../config/config.json"), true);
        $translation = [
            "basetext" =>
                json_decode(file_get_contents(__DIR__  . $config["translation_path"] . "base.json"), true),
        ];
        if ($translationFilename !== null) {
            $translation["text"] =
                json_decode(file_get_contents(__DIR__  . $config["translation_path"] . $translationFilename), true);
        }
        $args = array_merge($globalArgs, $userArgs, $translation);
        return $this->twig->render($templateFilename, $args);
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
                $this->addJsScript("$folderPath$path");
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
     * Add $variable to $this->renderVars.
     * It should be array like [ variableName => value ].
     */
    public function addRenderVar(array $variable): self
    {
        foreach ($variable as $key => $value) {
            $this->renderVars[$key] = $value;
        }
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

    /**
     * Zwraca n-ty parametr URL lub null, gdy nieokreślony.
     * Index liczony od 1.
     *
     * @return null|string
     */
    public function getUrlParam(int $index): ?string
    {
        return $this->url->getParam(--$index);
    }

    /**
     * Return application url without parameters.
     *
     * @return string
     */
    public function getUrlBase(): string
    {
        return $this->url->getBaseUrl();
    }

    /**
     * Return controller object.
     */
    public function getController(): ?Controller\Controller
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

    public function getRenderVars(): array
    {
        return $this->renderVars;
    }

    public function getDatabase(): ?Database
    {
        return $this->database;
    }

    /**
     * Set controller object.
     */
    public function setController(string $name): self
    {
        $controllerName = "pietras\\Controller\\{$name}Controller";
        $this->controller = new $controllerName();
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
}
