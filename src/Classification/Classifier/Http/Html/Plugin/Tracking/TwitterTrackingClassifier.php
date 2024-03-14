<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Tracking;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class TwitterTrackingClassifier extends HtmlClassifier
{
    private const PREFIX = 'html:plugin:tracking:twitter';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'Twitter universal website tag code',
            'Twitter Pixel ID',
            "twq('init','nygdn');"
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
