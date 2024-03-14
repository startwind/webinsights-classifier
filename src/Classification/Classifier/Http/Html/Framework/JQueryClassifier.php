<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Framework;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class JQueryClassifier extends HtmlClassifier
{
    const TAG_JQUERY = 'html:framework:jquery';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->contains('jquery-3.7.1.min.js')) {
            return [self::TAG_JQUERY . ':3', self::TAG_JQUERY];
        }

        if ($htmlDocument->contains('jquery')) {
            return [self::TAG_JQUERY];
        }

        return [];
    }
}
