<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class ShopifyClassifier extends HtmlClassifier
{
    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny(['pay.shopify.com', 'cdn.shopify.com'])) {
            return [EcommerceClassifier::TAG_ECOMMERCE, EcommerceClassifier::TAG_ECOMMERCE_SYSTEM_PREFIX . 'shopify'];
        }

        return [];
    }
}
