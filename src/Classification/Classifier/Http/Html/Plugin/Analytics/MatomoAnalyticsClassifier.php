<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Analytics;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class MatomoAnalyticsClassifier extends HtmlClassifier
{
    private const PREFIX = 'analytics:matomo';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'setConversionAttributionFirstReferrer',
            'Matomo'
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
