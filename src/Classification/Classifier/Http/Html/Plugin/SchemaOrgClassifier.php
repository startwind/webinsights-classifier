<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SchemaOrgClassifier extends HtmlClassifier
{
    const TAG = 'html:meta:schema-org';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(['<script type="application/ld+json">', ' "@context": "https://schema.org"'])) {
            return [self::TAG];
        } else {
            return [];
        }
    }
}
