<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce;

use Startwind\WebInsights\Classification\Classifier\Cms\SquareSpaceClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SquareSpaceCommerceClassifier extends HtmlClassifier
{
    private const TAG = EcommerceClassifier::TAG_ECOMMERCE_SYSTEM_PREFIX . 'squarespace-commerce';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'squarespace-commerce',
        ])) {
            return [
                self::TAG,
                ProgrammingLanguageClassifier::TAG_JAVA,
                SquareSpaceClassifier::TAG
            ];
        } else {
            return [];
        }
    }
}
