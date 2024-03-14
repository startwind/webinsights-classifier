<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http\Cdn;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class ImpervaClassifier extends HttpClassifier
{
    const TAG = 'tech:cdn:imperva';

    protected function doHttpClassification(HttpResponse $response): array
    {
        if ($response->headerContains('X-Cdn', 'imperva')) {
            return [self::TAG];
        } else {
            return [];
        }
    }
}
