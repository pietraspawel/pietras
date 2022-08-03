<?php

namespace pietras;

/**
 * Provide class Url.
 */
class Url
{

    /**
     * @var string $partToSkip Part of URI which is not considered.
     */
    private $partToSkip;
    /**
     * @var array List of url parameters.
     */
    private $param;

    /**
     * Class constructor.
     */
    public function __construct(string $partToSkip = "/")
    {
        $this->partToSkip = $partToSkip;
        $this->refresh();
    }

    /**
     * Get actual URL and split it to parameters.
     */
    public function refresh()
    {
        $partToSkip = preg_replace('/\//', '\\/', $this->partToSkip);
        $uri = preg_replace('/^' . $partToSkip . '/', '', $_SERVER["REQUEST_URI"]);
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
}
