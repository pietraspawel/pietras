<?php

namespace pietras;

/**
* Udostępnia metody umożliwiające sprawdzenie, czy uruchomienie skryptu jest dozwolone.
*/
class ScriptExecutor
{
    /**
    * Sprawdza, czy teraz jest $minute.
    */
    public static function isRightMinute(int $minute): bool
    {
        $date = new Date();
        if ($minute == $date->getMinute()) {
            return true;
        }        
        return false;
    }
}
