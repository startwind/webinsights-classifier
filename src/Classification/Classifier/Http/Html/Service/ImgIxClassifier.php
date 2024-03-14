<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Service;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class ImgIxClassifier implements Classifier
{
    const TAG_PREFIX = 'service:imgix';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'imgix.com', 'imgix.net'
        ])) {
            return [self::TAG_PREFIX];
        } else {
            return [];
        }
    }
}
