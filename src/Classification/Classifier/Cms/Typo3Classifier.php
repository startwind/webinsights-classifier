<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class Typo3Classifier extends HtmlClassifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'typo3';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(['typo3temp', 'typo3conf', 'This website is powered by TYPO3'])) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        } else {
            return [];
        }
    }
}
