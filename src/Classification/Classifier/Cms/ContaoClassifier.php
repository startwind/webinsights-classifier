<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Framework\SymfonyClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class ContaoClassifier implements Classifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'contao';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->hasHeader('Contao-Cache')) {
            return [self::TAG, SymfonyClassifier::TAG_PREFIX, ProgrammingLanguageClassifier::TAG_PHP];
        }

        if ($httpResponse->getHtmlDocument()->contains('Contao Open Source CMS')) {
            return [self::TAG, SymfonyClassifier::TAG_PREFIX, ProgrammingLanguageClassifier::TAG_PHP];
        }

        return [];
    }

}
