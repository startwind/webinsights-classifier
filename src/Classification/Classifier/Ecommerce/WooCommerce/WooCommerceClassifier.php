<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce\WooCommerce;

use Startwind\WebInsights\Classification\Classifier\Cms\WordPress\WordPressClassifier;
use Startwind\WebInsights\Classification\Classifier\Ecommerce\EcommerceClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class WooCommerceClassifier extends HtmlClassifier
{
    public const TAG = EcommerceClassifier::TAG_ECOMMERCE_SYSTEM_PREFIX . 'woocommerce';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->contains('plugins/woocommerce')) {
            return [
                self::TAG,
                EcommerceClassifier::TAG_ECOMMERCE,
                WordPressClassifier::TAG,
                ProgrammingLanguageClassifier::TAG_PHP
            ];
        } else {
            return [];
        }
    }
}
