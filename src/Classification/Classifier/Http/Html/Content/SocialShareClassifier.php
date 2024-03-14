<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SocialShareClassifier extends HtmlClassifier
{
    const TAG = 'html:content:share:';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        if ($htmlDocument->containsAny(['property="og:image'])) {
            $tags[] = self::TAG . 'open-graph';
        }

        if ($htmlDocument->containsAny(['name="twitter:title"'])) {
            $tags[] = self::TAG . 'twitter';
        }

        return $tags;
    }
}
