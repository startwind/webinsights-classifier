<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SquarespaceClassifier extends HtmlClassifier
{
    public const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'squarespace';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'static1.squarespace.com',
            'This is Squarespace'
        ])) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_JAVA];
        } else {
            return [];
        }
    }
}
