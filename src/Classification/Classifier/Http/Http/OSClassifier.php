<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class OSClassifier extends HttpClassifier
{
    const TAG_PREFIX = 'tech:os:';

    protected function doHttpClassification(HttpResponse $response): array
    {
        if ($response->headerContains('server', 'ubuntu')) {
            return [self::TAG_PREFIX . 'ubuntu'];
        }

        return [];
    }
}
