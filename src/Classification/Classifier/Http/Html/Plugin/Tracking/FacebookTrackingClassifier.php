<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Tracking;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class FacebookTrackingClassifier extends HtmlClassifier
{
    private const PREFIX = 'html:plugin:tracking:facebook';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'https://connect.facebook.net/en_US/fbevents.js',
            'Facebook Pixel Code',
            'https://www.facebook.com/tr?'
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
