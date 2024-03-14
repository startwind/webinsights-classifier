<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Service;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class CalendlyClassifier implements Classifier
{
    const TAG_PREFIX = 'service:calendly';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'calendly.com'
        ])) {
            return [self::TAG_PREFIX];
        } else {
            return [];
        }
    }
}
