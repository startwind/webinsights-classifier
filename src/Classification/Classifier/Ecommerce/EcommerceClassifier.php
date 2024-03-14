<?php

namespace Startwind\WebInsights\Classification\Classifier\Ecommerce;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class EcommerceClassifier extends HtmlClassifier
{
    const TAG_ECOMMERCE = 'type:ecommerce';
    const TAG_ECOMMERCE_SYSTEM_PREFIX = 'ecommerce:system:';

    private array $keywords = [
        // english
        "Shop now",
        "My wishlist",
        "shipping info",
        "american-express",
        "american_express",
        "Track My Order",
        "Shipping and Delivery",
        "Payment options",

        // german
        "Warenkorb",
        "Versandkosten",
        "Lieferbedingungen",
        "Verkaufsbedingungen",
        "Retoure",
        "Versand & Lieferung",
        "Zahlungsarten"
    ];

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        foreach ($this->keywords as $keyword) {
            if ($htmlDocument->contains($keyword)) return [self::TAG_ECOMMERCE];
        }

        return [];
    }
}
