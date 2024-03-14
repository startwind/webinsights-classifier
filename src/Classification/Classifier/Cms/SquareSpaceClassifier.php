<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SquareSpaceClassifier extends HtmlClassifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'squarespace';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(['static1.squarespace.com'])) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_JAVA];
        } else {
            return [];
        }
    }
}
