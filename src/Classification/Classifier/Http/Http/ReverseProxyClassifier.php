<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class ReverseProxyClassifier implements Classifier
{
    const TAG_PREFIX = 'tech:reverse-proxy:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->headerContains('via', 'varnish')) {
            return [self::TAG_PREFIX . 'varnish'];
        }

        return [];
    }
}
