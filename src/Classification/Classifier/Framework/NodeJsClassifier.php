<?php

namespace Startwind\WebInsights\Classification\Classifier\Framework;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class NodeJsClassifier extends HttpClassifier
{
    const TAG_PREFIX = 'backend:framework:nodejs';

    protected function doHttpClassification(HttpResponse $response): array
    {
        return [];
    }
}
