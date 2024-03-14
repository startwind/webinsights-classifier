<?php

namespace Startwind\WebInsights\Classification\Classifier;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

abstract class UrlClassifier implements Classifier
{
    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        return $this->doClassification($httpResponse->getRequestUri(), $existingTags);
    }

    abstract protected function doClassification(UriInterface $uri, array $existingTags): array;
}
