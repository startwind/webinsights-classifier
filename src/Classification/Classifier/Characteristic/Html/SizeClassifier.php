<?php

namespace Startwind\WebInsights\Classification\Classifier\Characteristic\Html;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SizeClassifier extends HtmlClassifier
{
    const TAG = 'characteristic:html:size_mb:';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->getPlainContent() === "") {
            return [];
        }
        $sizeInKb = mb_strlen($htmlDocument->getPlainContent(), '8bit');
        $sizeInMb = ceil($sizeInKb / 1000 / 1000);

        return [self::TAG . $sizeInMb];
    }
}
