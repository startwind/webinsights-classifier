<?php

namespace Startwind\WebInsights\Classification\Helper;

use Startwind\WebInsights\Response\Html\HtmlDocument;

abstract class XPathHelper
{
    public static function exists(HtmlDocument $htmlDocument, array $xPaths, string $prefix = ''): array
    {
        $domDocument = $htmlDocument->asDomDocument();

        $domXpath = new \DOMXpath($domDocument);

        $tags = [];

        foreach ($xPaths as $tag => $xPathArray) {
            if (is_string($xPathArray)) {
                $xPathArray = [$xPathArray];
            }

            foreach ($xPathArray as $xPath) {
                $elements = $domXpath->query($xPath);
                if ($elements->length > 0) $tags[] = $prefix . $tag;
            }
        }

        return $tags;
    }

    public static function value(HtmlDocument $htmlDocument, string $xPath, string $prefix = ''): string|false
    {
        $domDocument = $htmlDocument->asDomDocument();

        $domXpath = new \DOMXpath($domDocument);

        $elements = $domXpath->query($xPath);

        if ($elements->length > 0) return $prefix . $elements[0]->value;

        return false;
    }
}
