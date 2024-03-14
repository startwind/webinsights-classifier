<?php

namespace Startwind\WebInsights\Util;

abstract class FilenameHelper
{
    static public function process(string $filename, array $additionalParameters = []): string
    {
        $additionalParameters['timestamp'] = time();
        $additionalParameters['uuid'] = self::getGuid();

        foreach ($additionalParameters as $key => $value) {
            $filename = str_replace('{' . $key . '}', $value, $filename);
        }

        return $filename;
    }

    private static function getGuid(): string
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
