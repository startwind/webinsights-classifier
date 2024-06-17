<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Seo;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class SeoAntiPatternClassifier implements Classifier
{
    const TAG = 'html:content:seo:anti-pattern';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $tags = [];

        $headlineCount = $httpResponse->getHtmlDocument()->countTextOccurrences('<h1');

        if ($headlineCount > 1) {
            $tags[] = self::TAG;
            $tags[] = self::TAG . ':' . 'multiple-h1';

        }

        $titleCount = $httpResponse->getHtmlDocument()->countTextOccurrences('<title');

        if ($titleCount != 1) {
            $tags[] = self::TAG;
            $tags[] = self::TAG . ':' . 'no-title';
        }

        return $tags;
    }
}
