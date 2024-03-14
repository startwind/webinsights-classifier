<?php

namespace Startwind\WebInsights\Classification\Classifier\Http;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

abstract class HttpClassifier implements Classifier
{
    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        return $this->doHttpClassification($httpResponse);
    }

    abstract protected function doHttpClassification(HttpResponse $response): array;
}
