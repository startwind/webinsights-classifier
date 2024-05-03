<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class StatusCodeClassifier implements Classifier
{
    public const TAG_PREFIX = 'http:status-code:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        return [self::TAG_PREFIX . $httpResponse->getStatusCode()];
    }
}
