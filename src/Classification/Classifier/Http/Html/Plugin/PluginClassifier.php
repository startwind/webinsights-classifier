<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class PluginClassifier extends HtmlClassifier
{
    public const PREFIX = 'html:plugin:';
    public const PREFIX_MONITORING = 'html:plugin:monitoring:';

    private array $plugins = [
        'borlabs-cookies' => 'borlabs-cookie',

        'font-awesome' => ['fontawesome.com', 'font-awesome.min.css'],
        'bugherd' => 'bugherd.com',
        'kameleoon' => 'kameleoon.eu/kameleoon.js',
        'klarna' => 'klarna.com/web-sdk',

        'tracking:etracker' => 'etracker tracklet',

        'cookie:cookie-bot' => 'consent.cookiebot.com',
        'cookie:cookiehub' => 'cookiehub.eu',
        'cookie:cookiefirst' => '<https://consent.cookiefirst.com/banner.js',
        'cookie:cookielaw' => 'cdn.cookielaw.org',
        'cookie:ccm19' => 'CCM19',

        'mobile-app:apple' => 'apple-itunes-app',

        'whatsapp' => 'api.whatsapp.com',
        'youtube-video' => 'src="https://www.youtube.com/embed',

        self::PREFIX_MONITORING . 'sentry' => ['sentryFetchProxy', 'Sentry.init'],
        self::PREFIX_MONITORING . 'new-relic' => '"New Relic: "',
        self::PREFIX_MONITORING . 'site24x7' => 'site24x7rum-min.js',
        self::PREFIX_MONITORING . 'appdynamics' => ['cdn.appdynamics.com', 'appdynamics'],
        self::PREFIX_MONITORING . 'datadog' => '"datadog":{"rumClientToken":',

        'a-b-testing:varify' => 'app.varify.io/varify.js',

        'chat:tawk.to' => 'Tawk_API',
        'chat:livechat' => 'cdn.livechatinc.com',
        'chat:zendesk' => 'https://static.zdassets.com/ekr/snippet.js',
        'chat:comm100' => 'javascript:comm100_livechat',

        'captcha:hcaptcha' => "href='//hcaptcha.com'",
        'fontello' => 'fontello.min.css',
        'affiliate:awin' => 'https://www.dwin1.com',
    ];

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        foreach ($this->plugins as $key => $plugins) {
            if (!is_array($plugins)) {
                $plugins = [$plugins];
            }

            foreach ($plugins as $plugin) {
                if ($htmlDocument->contains($plugin)) {
                    $tags[] = self::PREFIX . $key;
                }
            }
        }

        return $tags;
    }
}
