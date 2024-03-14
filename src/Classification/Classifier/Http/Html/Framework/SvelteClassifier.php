<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Framework;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SvelteClassifier extends HtmlClassifier
{
    const TAG = 'html:framework:svelte';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(['class="svelte-', '_app/immutable/'])) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_JS];
        } else {
            return [];
        }
    }
}
