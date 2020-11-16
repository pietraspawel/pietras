<?php

namespace pietras;

/**
* Udostępnia metody pomocne przy pisaniu skryptów konsolowych.
*/
class ConsoleMethods
{
    /**
    * Konwertuje $input na właściwy typ.
    * Jeśli $input jest, zwraca:
    * - w cudzysłowiu - string,
    * - true - true,
    * - false - false,
    * - null - null,
    * - w pozostałych przypadkach zwraca liczbę
    */
    public static function convertToType(string $input)
    {
        if ($input[0] == "\"" && $input[strlen($input) - 1] == "\"") {
            return substr($input, 1, strlen($input) - 2);
        } elseif ($input == "true") {
            return true;
        } elseif ($input == "false") {
            return false;
        } elseif ($input == "null") {
            return null;
        } else {
            return $input * 1;
        }
    }
}
