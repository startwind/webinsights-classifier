<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Analytics;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class PlausibleAnalyticsClassifier extends HtmlClassifier
{
    private const PREFIX = 'analytics:plausible';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'https://plausible.io/js/plausible.js'
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
