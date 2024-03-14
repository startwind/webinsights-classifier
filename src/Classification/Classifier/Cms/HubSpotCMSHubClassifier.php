<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class HubSpotCMSHubClassifier implements Classifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'hubspot-cms-hub';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'HubSpot Styles',
            'name="generator" content="HubSpot"',
            '/hs/hsstatic'
        ])) {
            return [self::TAG];
        }

        return [];
    }
}
