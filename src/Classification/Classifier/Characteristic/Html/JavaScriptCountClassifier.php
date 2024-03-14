<?php

namespace Startwind\WebInsights\Classification\Classifier\Characteristic\Html;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class JavaScriptCountClassifier extends HtmlClassifier
{
    const TAG = 'characteristic:html:javascript:count:';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $matches = $htmlDocument->match('#src="(.*?)\.js(.*?)"#');
        return [self::TAG . ceil(count($matches) / 10) * 10];
    }
}
