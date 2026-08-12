<?php

declare(strict_types=1);

namespace pietras\basic;

use Symfony\Component\Yaml\Yaml;

/**
 * Klasa abstrakcyjna dla kontrolerów.
 */
abstract class Controller
{
    protected application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Tu zaczyna się obsługa danej routy.
     */
    abstract public function handle();
}
