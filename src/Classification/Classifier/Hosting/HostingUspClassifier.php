<?php

namespace Startwind\WebInsights\Classification\Classifier\Hosting;

use Startwind\WebInsights\Classification\Classifier\PatternAwareClassifier;

class HostingUspClassifier extends PatternAwareClassifier
{
    public const TAG_PREFIX = 'company:hosting:usp:';

    protected array $keywords = [
        self::SOURCE_HTML => [
            self::TAG_PREFIX . 'ripe' => 'ripe-ncc',
            self::TAG_PREFIX . 'denic' => 'denic',
            self::TAG_PREFIX . 'money-back' => ['money back', 'geld-zurück', 'geld zurück'],
            self::TAG_PREFIX . 'eco-friendly' => ['ökostrom', 'oekostrom'],
        ]
    ];
}
