<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Analytics;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class HubSpotAnalyticsClassifier extends HtmlClassifier
{
    private const PREFIX = 'analytics:hubspot';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'HubSpot Analytics Code', 'is_hubspot_user'
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
