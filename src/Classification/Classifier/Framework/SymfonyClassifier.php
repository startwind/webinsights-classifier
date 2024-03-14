<?php

namespace Startwind\WebInsights\Classification\Classifier\Framework;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class SymfonyClassifier extends HttpClassifier
{
    const TAG_PREFIX = 'backend:framework:symfony';

    protected function doHttpClassification(HttpResponse $response): array
    {
        return [];
    }
}
