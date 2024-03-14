<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class JoomlaClassifier implements Classifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'joomla';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'Joomla! - Open Source Content Management',
            'joomla-favicon.svg',
            'joomla-fontawesome',
            'joomla-script-options',
            '/media/system',
            'joomspirit',
            'JoomlaWorks'
        ])) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        }

        return [];
    }
}
