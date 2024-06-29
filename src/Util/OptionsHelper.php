<?php

namespace Startwind\WebInsights\Util;

use Startwind\WebInsights\Configuration\Exception\MissingOptionException;

abstract class OptionsHelper
{
    public static function assertValid($options, $fields): void
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $options)) {
                throw new MissingOptionException('The mandatory option "' . $field . '" is missing.');
            }
        }
    }
}
