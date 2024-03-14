<?php

namespace Startwind\WebInsights\Classification\Classifier\Test;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class TestClassifier implements Classifier
{
    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        return [];
    }

}
