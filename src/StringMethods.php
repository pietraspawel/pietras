<?php

namespace pietras;

/**
* Operacje na łańcuchach.
*/
class StringMethods
{
    public const LOWERCASE = 1;

    /**
    * Konwertuje polskie litery na łacińskie.
    * Może przyjmować dodatkowy argument StringMethods::LOWERCASE, zamienia wtedy łańcuch na lowercase.
    */
    public static function plToLatin(string $string): string
    {
        $string = preg_replace('/Ę/', 'E', $string);
        $string = preg_replace('/ę/', 'e', $string);
        $string = preg_replace('/Ó/', 'O', $string);
        $string = preg_replace('/ó/', 'o', $string);
        $string = preg_replace('/Ą/', 'A', $string);
        $string = preg_replace('/ą/', 'a', $string);
        $string = preg_replace('/Ś/', 'S', $string);
        $string = preg_replace('/ś/', 's', $string);
        $string = preg_replace('/Ł/', 'L', $string);
        $string = preg_replace('/ł/', 'l', $string);
        $string = preg_replace('/Ż/', 'Z', $string);
        $string = preg_replace('/ż/', 'z', $string);
        $string = preg_replace('/Ź/', 'Z', $string);
        $string = preg_replace('/ź/', 'z', $string);
        $string = preg_replace('/Ć/', 'C', $string);
        $string = preg_replace('/ć/', 'c', $string);
        $string = preg_replace('/Ń/', 'N', $string);
        $string = preg_replace('/ń/', 'n', $string);

        if (func_num_args() > 1 && func_get_arg(1) == self::LOWERCASE) {
            $string = strtolower($string);
        }

        return $string;
    }

    /**
    * Porównuje dwa łańcuchy i zwraca procent niezgodności.
    * Nie są uwzględniane białe znaki.
    * Nie liczy się case.
    * PLiterki są zamieniane na łacińskie.
    */
    public static function compareText(string $text1, string $text2): float
    {
        $text1 = preg_replace("/\s+/", "", $text1);
        $text2 = preg_replace("/\s+/", "", $text2);
        $text1 = self::plToLatin($text1, self::LOWERCASE);
        $text2 = self::plToLatin($text2, self::LOWERCASE);
        $percent = 0.0;
        similar_text($text1, $text2, $percent);
        return $percent;
    }

    /**
    * Zamienia camelCase na snake_case.
    */
    public static function camelToSnake(string $string): string
    {
        $ret = "";
        for ($i=0; $i < strlen($string); $i++) { 
            if (ctype_upper($string[$i])) {
                $ret .= "_" . strtolower($string[$i]);
            } else {
                $ret .= $string[$i];
            }
        }
        return $ret;
    }
}
