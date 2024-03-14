<?php

namespace Startwind\WebInsights\Util;

abstract class TagHelper
{
    static public function normalize(string $string): string
    {
        $string = strtolower($string);
        $string = str_replace(' ', '_', $string);
        $string = str_replace(',', '_', $string);
        $string = str_replace('.', '_', $string);

        $string = str_replace('___', '_', $string);
        $string = str_replace('__', '_', $string);

        $string = trim($string);
        $string = trim($string, '_');

        return $string;
    }
}
