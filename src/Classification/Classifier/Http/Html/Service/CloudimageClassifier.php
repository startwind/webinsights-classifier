<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Service;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class CloudimageClassifier implements Classifier
{
    const TAG_PREFIX = 'service:cloudimage';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'cloudimg.io'
        ])) {
            return [self::TAG_PREFIX];
        } else {
            return [];
        }
    }
}
