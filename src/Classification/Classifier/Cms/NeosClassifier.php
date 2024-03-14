<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Framework\FlowClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class NeosClassifier extends HtmlClassifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'neos';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(["_Resources/Static/Packages/", 'name="neos-version"', 'powered by Neos'])) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP, FlowClassifier::TAG_PREFIX];
        } else {
            return [];
        }
    }
}
