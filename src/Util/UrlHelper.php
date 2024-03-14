<?php

namespace Startwind\WebInsights\Util;

use Psr\Http\Message\UriInterface;

abstract class UrlHelper
{
    static public function getDomain(UriInterface $uri): string
    {
        $host_with_subdomain = $uri->getHost();
        $array = explode(".", $host_with_subdomain);

        return (array_key_exists(count($array) - 2, $array) ? $array[count($array) - 2] : "") . "." . $array[count($array) - 1];
    }
}
