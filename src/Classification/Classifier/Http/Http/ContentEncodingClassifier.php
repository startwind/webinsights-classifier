<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class ContentEncodingClassifier extends HttpClassifier
{
    const TAG_PREFIX = 'tech:http:content-encoding:';

    protected function doHttpClassification(HttpResponse $response): array
    {
        if ($response->hasHeader('Content-Encoding')) {
            foreach ($response->getHeader('Content-Encoding') as $header) {
                return [self::TAG_PREFIX . strtolower($header)];
            }
        }

        return [];
    }
}
