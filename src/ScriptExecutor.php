<?php

namespace pietras;

/**
* Udostępnia metody umożliwiające sprawdzenie, czy uruchomienie skryptu jest dozwolone.
*/
class ScriptExecutor
{
    /**
    * Skrypt jest uruchamiany, gdy obecna minuta jest minutą z $filename.
    * Kolejne uruchomienie jest za losową liczbę minut, losowaną z przedziału
    * od $minRange do $maxRange.
    * Liczba jest zapisywana do pliku.
    */
    public static function randomMinuteFromFile(string $filename, int $minRange, int $maxRange): bool
    {
        $rightMinute = file_get_contents($filename);
        if (self::isRightMinute($rightMinute)) {
            $nextMinute = $rightMinute + mt_rand($minRange, $maxRange);
            if ($nextMinute >= 60) {
                $nextMinute -= 60;
            }
            file_put_contents($filename, $nextMinute);
            return true;
        }
        return false;
    }

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
