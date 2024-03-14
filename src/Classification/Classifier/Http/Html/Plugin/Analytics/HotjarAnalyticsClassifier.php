<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Analytics;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class HotjarAnalyticsClassifier extends HtmlClassifier
{
    private const PREFIX = 'analytics:hotjar';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'https://static.hotjar.com'
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
