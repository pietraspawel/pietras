<?php

namespace pietras\basic;

/**
 * Provide class Url.
 */
class Url
{

    /**
     * @var string $baseUrl Root.
     */
    private $baseUrl;
    /**
     * @var array List of url parameters.
     */
    private $param;

    /**
     * Class constructor.
     */
    public function __construct(string $baseUrl = "/")
    {
        $this->baseUrl = $baseUrl;
        $this->refresh();
    }

    /**
     * Get actual URL and split it to parameters.
     */
    public function refresh()
    {
        $baseUrl = str_replace("\/", "/", $this->baseUrl);
        $baseUrl = str_replace("http://", "", $baseUrl);
        $baseUrl = str_replace("https://", "", $baseUrl);
        $uri = str_replace($baseUrl, "", $_SERVER["HTTP_HOST"] .  $_SERVER["REQUEST_URI"]);
        $this->param = explode("/", $uri);
        foreach ($this->param as $key => $value) {
            if (strpos($value, "?") !== false) {
                $value = substr($value, 0, strpos($value, "?"));
            }
            $this->param[$key] = urldecode($value);
        }
    }

    /**
     * Return URL parameter.
     *
     * @param  int    $key
     * @return string|null
     */
    public function getParam(int $key): ?string
    {
        return $this->param[$key] ?? "";
    }

    /**
     * Return full URI.
     */
    public function getFullUrl(): string
    {
        return $_SERVER["REQUEST_URI"];
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
