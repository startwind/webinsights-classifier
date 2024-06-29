<?php

namespace Startwind\WebInsights\Classification\Classifier\Static;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class StaticClassifier implements Classifier
{
    private array $tags;

    public function init(array $options)
    {
        $this->tags = $options['tags'];
    }

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        return $this->tags;
    }
}
