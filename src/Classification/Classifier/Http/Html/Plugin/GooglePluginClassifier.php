<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class GooglePluginClassifier extends HtmlClassifier
{
    private const PREFIX = 'html:plugin:google';
    private array $plugins = [
        'analytics' => ['google-analytics', 'var gaProperty'],
        'tag-manager' => 'googletagmanager.com/gtm.js',
        'fonts' => ['fonts.googleapis.com', 'https://fonts.gstatic.com'],
        'maps' => 'https://maps.googleapis.com',
        'ads' => 'adsbygoogle.js',
        'firebase' => 'firebase-app.js',
        'firebase:messaging' => 'firebase-messaging.js',
        'recaptcha' => 'https://www.google.com/recaptcha/api.js',
        'doubleclick' => 'https://securepubads.g.doubleclick.net',
        'cloud-storage' => 'storage.googleapis.com'
    ];

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        foreach ($this->plugins as $key => $plugins) {
            if (!is_array($plugins)) {
                $plugins = [$plugins];
            }
            foreach($plugins as $plugin) {
                if ($htmlDocument->contains($plugin)) {
                    $tags[] = self::PREFIX . Classifier::TAG_SEPARATOR . $key;
                }
            }
        }

        return $tags;
    }
}
