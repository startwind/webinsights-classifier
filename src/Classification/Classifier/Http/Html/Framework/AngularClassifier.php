<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Framework;

use Startwind\WebInsights\Classification\Classifier\Framework\NodeJsClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class AngularClassifier extends HtmlClassifier
{
    const TAG = 'html:framework:angular';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'ng-init="',
        ])) {
            return [self::TAG, NodeJsClassifier::TAG_PREFIX, ProgrammingLanguageClassifier::TAG_JS];
        }

        return [];
    }
}
