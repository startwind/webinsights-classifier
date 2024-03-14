<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Framework;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class BootstrapClassifier extends HtmlClassifier
{
    const TAG = 'html:framework:bootstrap';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'bootstrap.css',
            'bootstrap.min.js',
            'Bootstrap customization'
        ])) {
            return [self::TAG];
        }

        return [];
    }
}
