<?php

declare(strict_types=1);

namespace pietras\basic;

/**
 * Represents current request URL.
 */
class Url
{
    // Dla URL: https://abc.def/project/ghi/jkl?mno=123
    // app url, np: https://abc.def/project
    private string $appUrl;
    // base url = /project
    private string $baseUrl;
    // full url: https://abc.def/project/ghi/jkl?mno=123
    private string $fullUrl;
    // uri = /ghi/jkl?mno=123
    private string $uri;
    // path = /ghi/jkl
    private string $path;
    // params[0] = "ghi", params[1] = "jkl"
    private array $params;

    public function __construct(string $appUrl)
    {
        $this->appUrl = rtrim($appUrl, "/");
        $this->baseUrl = rtrim(parse_url($this->appUrl, PHP_URL_PATH) ?? "", "/");
        $requestUri = $_SERVER["REQUEST_URI"] ?? "/";
        if (
            $this->baseUrl !== "" &&
            (
                $requestUri === $this->baseUrl ||
                strpos($requestUri, $this->baseUrl . "/") === 0
            )
        ) {
            $this->uri = substr($requestUri, strlen($this->baseUrl));
        } else {
            $this->uri = $requestUri;
        }
        if ($this->uri === "") {
            $this->uri = "/";
        }
        $this->fullUrl = rtrim($this->appUrl, "/");
        if ($this->uri != "/") {
            $this->fullUrl .= $this->uri;
        }
        $this->path = parse_url($this->uri, PHP_URL_PATH) ?? "/";
        $this->params = array_values(
            array_filter(
                explode("/", trim($this->path, "/")),
                "strlen"
            )
        );
    }

    public function getAppUrl(): string
    {
        return $this->appUrl;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getFullUrl(): string
    {
        return $this->fullUrl;
    }

    /**
     * Returns URI relative to base URL.
     *
     * Example:
     * /users/15?page=2
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Returns path without query string.
     *
     * Example:
     * /users/15
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns all path parameters.
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Returns one path parameter or empty string.
     */
    public function getParam(int $index): ?string
    {
        return $this->params[$index] ?? null;
    }

    /**
     * Returns number of path parameters.
     */
    public function countParams(): int
    {
        return count($this->params);
    }

    /**
     * Sprawdza, czy parametr[$index] === $value.
     * Zwraca false, również, jeśli parametr[$index] nie istnieje.
     */
    public function paramNEquals(int $index, $value): bool
    {
        if (!isset($this->params[$index])) {
            return false;
        }
        if ($this->params[$index] === $value) {
            return true;
        }
        return false;
    }
}
