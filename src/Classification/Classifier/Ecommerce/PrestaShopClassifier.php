<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class PrestaShopClassifier extends HtmlClassifier
{
    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(['PrestaShop', 'var prestashop'])) {
            return [EcommerceClassifier::TAG_ECOMMERCE, EcommerceClassifier::TAG_ECOMMERCE_SYSTEM_PREFIX . 'prestashop', ProgrammingLanguageClassifier::TAG_PHP];
        }

        return [];
    }
}
