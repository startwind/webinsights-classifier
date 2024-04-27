<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\Tracking;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class AdCellTrackingClassifier extends HtmlClassifier
{
    private const PREFIX = 'html:plugin:tracking:adcell';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'Adcell.Tracking.track()',
            'adcell.com/js/trad.js'
        ])) {
            return [self::PREFIX];
        }
        return [];
    }
}
