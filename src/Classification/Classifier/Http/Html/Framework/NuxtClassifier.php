<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Framework;

use Startwind\WebInsights\Classification\Classifier\Framework\NodeJsClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class NuxtClassifier extends HtmlClassifier
{
    const TAG_NUXT = 'html:framework:nuxt';
    const TAG_VUE = 'html:framework:vue';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->contains('_nuxt')) {
            return [self::TAG_NUXT, self::TAG_VUE, NodeJsClassifier::TAG_PREFIX, ProgrammingLanguageClassifier::TAG_JS];
        } else {
            return [];
        }
    }
}
