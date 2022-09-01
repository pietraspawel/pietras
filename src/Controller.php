<?php

namespace pietras\basic;

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
    /**
     * Tu zaczyna się obsługa danej routy.
     */
    abstract public function handle();
}
