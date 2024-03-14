<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class BigCommerceClassifier extends HtmlClassifier
{
    private const TAG = EcommerceClassifier::TAG_ECOMMERCE_SYSTEM_PREFIX . 'bigcommerce';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(['bigcommerce.com'])) {
            return [
                self::TAG,
                EcommerceClassifier::TAG_ECOMMERCE,
                ProgrammingLanguageClassifier::TAG_PHP
            ];
        } else {
            return [];
        }
    }
}
