<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms\WordPress;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class WordPressThemeClassifier extends HtmlClassifier
{
    private const TAG = 'wordpress:theme:';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        $matches = $htmlDocument->match('^wp-content/themes/(.*?)/^');

        foreach ($matches as $match) {
            if (strlen($match) < 50) {
                $tags[] = self::TAG . strtolower($match);
                $tags[] = WordPressClassifier::TAG;
                $tags[] = ProgrammingLanguageClassifier::TAG_PHP;
            }
        }

        return $tags;
    }
}
