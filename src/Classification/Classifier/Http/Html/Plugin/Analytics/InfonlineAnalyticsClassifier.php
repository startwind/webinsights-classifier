<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Analytics;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class InfonlineAnalyticsClassifier implements Classifier
{
    private const PREFIX = 'analytics:infonline';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'script.ioam.de'
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
