<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Framework\SymfonyClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SitejetClassifier extends HtmlClassifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'sitejet';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'webcard.apiHost', 'api.sitehub.io'
        ])) {
            return [
                self::TAG,
                ProgrammingLanguageClassifier::TAG_PHP,
                SymfonyClassifier::TAG_PREFIX
            ];
        } else {
            return [];
        }
    }
}
