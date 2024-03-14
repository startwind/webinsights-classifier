<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class MagentoClassifier extends HtmlClassifier
{
    private const TAG = EcommerceClassifier::TAG_ECOMMERCE_SYSTEM_PREFIX . 'magento';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            'Magento_PageBuilder',
            'Magento_Theme',
            'x-magento-init'
        ])) {
            return [EcommerceClassifier::TAG_ECOMMERCE, self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        } else {
            return [];
        }
    }
}
