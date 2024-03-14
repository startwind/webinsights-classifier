<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class DrupalClassifier implements Classifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'drupal';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->headerContains('X-Generator', 'drupal')) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        }

        if ($httpResponse->getHtmlDocument()->containsAny(['/sites/all/'])) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        }

        if ($httpResponse->getHtmlDocument()->containsRegEx('^Drupal (.*) \(https:\/\/www.drupal.org\)^')) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        }

        if ($httpResponse->getHtmlDocument()->containsRegEx('^Drupal (.*) \(http:\/\/drupal.org\)^')) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        }

        return [];
    }
}
