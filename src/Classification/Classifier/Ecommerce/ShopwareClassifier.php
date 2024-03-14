<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Framework\SymfonyClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class ShopwareClassifier implements Classifier
{
    private const TAG = EcommerceClassifier::TAG_ECOMMERCE_SYSTEM_PREFIX . 'shopware';
    private const TAG_ADMIN = 'ecommerce:system:shopware:admin';

    private bool $isDetailedClassification = false;

    public function init(array $options): void
    {
        if (array_key_exists('detailed', $options)) {
            $this->isDetailedClassification = $options['detailed'];
        }
    }

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            "Realisiert mit Shopware",
            "icon-shopware",
            "icons-default-shopware"
        ])) {
            return [
                self::TAG,
                EcommerceClassifier::TAG_ECOMMERCE,
                SymfonyClassifier::TAG_PREFIX,
                ProgrammingLanguageClassifier::TAG_PHP
            ];
        }

        if ($this->isDetailedClassification && $this->doAdminClassification($httpResponse->getRequestUri())) {
            return [
                self::TAG,
                self::TAG_ADMIN,
                EcommerceClassifier::TAG_ECOMMERCE,
                SymfonyClassifier::TAG_PREFIX,
                ProgrammingLanguageClassifier::TAG_PHP
            ];
        } else {
            return [];
        }
    }


    protected function doAdminClassification(UriInterface $uri): bool
    {
        $loginUrl = $uri->withPath('/admin');

        try {
            $response = $this->getHttpClient()->get($loginUrl);
        } catch (\Exception $exception) {
            return false;
        }

        if ($response->getStatusCode() === 404) {
            return false;
        }

        return $response->getHtmlDocument()->contains('shopware');
    }
}
