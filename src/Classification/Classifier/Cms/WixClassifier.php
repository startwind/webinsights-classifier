<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class WixClassifier extends HtmlClassifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'wix';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(['Wix.com Website Builder', 'static.wixstatic.com'])) {
            return [
                self::TAG,
                ProgrammingLanguageClassifier::TAG_SCALA
            ];
        } else {
            return [];
        }
    }
}
