<?php

namespace Startwind\WebInsights\Classification\Classifier\Hosting;

use Startwind\WebInsights\Classification\Classifier\PatternAwareClassifier;

class HostingUspClassifier extends PatternAwareClassifier
{
    public const TAG_PREFIX = 'company:hosting:usp:';

    protected array $keywords = [
        self::SOURCE_HTML => [
            'ripe' => 'ripe-ncc',
            'denic' => 'denic',
            'money-back' => ['money back', 'geld-zurück', 'geld zurück'],
            'eco-friendly' => ['ökostrom', 'oekostrom'],
            'webpros' => 'WebPros',
            'whmcs' => 'whmcs',
            'solus' => 'solus',
        ]
    ];
}
