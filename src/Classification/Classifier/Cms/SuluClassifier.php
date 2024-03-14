<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Framework\SymfonyClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class SuluClassifier implements Classifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'sulu';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->headerContains('x-generator', 'sulu')) {
            return [self::TAG, SymfonyClassifier::TAG_PREFIX];
        }

        return [];
    }
}
