<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce\WooCommerce;

use Startwind\WebInsights\Classification\Classifier\Cms\WordPress\WordPressClassifier;
use Startwind\WebInsights\Classification\Classifier\Ecommerce\EcommerceClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class WooCommercePluginClassifier extends HttpClassifier
{
    private const TAG = 'woocommerce:plugin:';

    private array $htmlPlugins = [
        'yith-woocommerce-ajax-navigation' => 'yith-woocommerce-ajax-navigation',
        'ajax-search-for-woocommerce' => 'ajax-search-for-woocommerce',
        'load-more-products-for-woocommerce' => 'load-more-products-for-woocommerce'
    ];

    protected function doHttpClassification(HttpResponse $response): array
    {
        $tags = [];

        foreach ($this->htmlPlugins as $key => $plugin) {
            if ($response->getHtmlDocument()->contains('wp-content/plugins/' . $plugin)) {
                $tags[] = self::TAG . $key;
                $tags[] = EcommerceClassifier::TAG_ECOMMERCE;
                $tags[] = WooCommerceClassifier::TAG;
                $tags[] = WordPressClassifier::TAG;
                $tags[] = ProgrammingLanguageClassifier::TAG_PHP;
            }
        }

        return $tags;
    }
}
