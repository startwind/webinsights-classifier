<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\ControlPanel\cPanelClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class CPanelSitePublisherClassifier extends HtmlClassifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'cpanel-site-publisher';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'This code is subject to the cPanel license'
        ])) {
            return [
                self::TAG,
                cPanelClassifier::TAG,
                ProgrammingLanguageClassifier::TAG_PERL
            ];
        } else {
            return [];
        }
    }
}
