<?php

namespace pietras\basic;

use Symfony\Component\Yaml\Yaml;

/**
 * Klasa abstrakcyjna dla kontrolerów.
 */
abstract class Controller
{
    /**
     * @var Application $application Przechowuje obiekt aplikacji.
     */
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public static function findControllerByUrl(Application $application, string $url): ?string
    {
        $path = $application->getConfig()["routes_path"];
        $routes = Yaml::parseFile($path);
        foreach ($routes as $controller => $urlsArray) {
            foreach ($urlsArray as $value) {
                if ($value == $url) {
                    return $controller;
                }
            }
        }
        return null;
    }

    /**
     * Tu zaczyna się obsługa danej routy.
     */
    abstract public function handle();
}
