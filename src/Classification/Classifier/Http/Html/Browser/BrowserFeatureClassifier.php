<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Browser;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class BrowserFeatureClassifier extends HtmlClassifier
{
    private const PREFIX = 'browser:feature:';

    private array $plugins = [
        'server-worker' => 'navigator.serviceWorker.register',
        'mailto' => 'href="mailto:',
        'apple-touch-icon' => 'apple-touch-icon',
        'web-app' => 'link rel="manifest"',
        'responsive-images' => 'data-srcset',
        'iframe' => '<iframe',
        'geo-information' => 'name="geo.position"',
        'telephone-link' => 'href="tel:',
    ];

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        foreach ($this->plugins as $key => $plugin) {
            if ($htmlDocument->contains($plugin)) {
                $tags[] = self::PREFIX . $key;
            }
        }

        return $tags;
    }
}
