<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Theme;

use Startwind\WebInsights\Classification\Classifier\PatternAwareClassifier;

class ThemeClassifier extends PatternAwareClassifier
{
    const TAG_PREFIX = 'html:theme:';

    protected array $keywords = [
        self::SOURCE_HTML => [
            'html5-up' => 'Identity by HTML5 UP'
        ]
    ];
}
