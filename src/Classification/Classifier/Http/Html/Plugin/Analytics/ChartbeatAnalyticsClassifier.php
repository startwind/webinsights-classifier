<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Analytics;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class ChartbeatAnalyticsClassifier extends HtmlClassifier
{
    private const PREFIX = 'analytics:chartbeat';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'chartbeat.com'
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
