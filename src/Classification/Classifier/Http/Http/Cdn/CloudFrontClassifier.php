<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http\Cdn;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class CloudFrontClassifier extends HttpClassifier
{
    const TAG_PREFIX = 'tech:cdn:';

    protected function doHttpClassification(HttpResponse $response): array
    {
        return [];
    }
}
