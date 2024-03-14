<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Meta;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class KeywordClassifier implements Classifier
{
    public const PREFIX = 'html:meta:keyword:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $dom = $httpResponse->getHtmlDocument()->asDomDocument();

        $tags = [];

        foreach ($dom->getElementsByTagName('meta') as $tag) {
            if ($tag->getAttribute('name') === 'keywords') {
                foreach (explode(',', $tag->getAttribute('content')) as $keyword) {
                    if (strlen($keyword) < 30) {
                        $tags[] = self::PREFIX . str_replace(' ', '_', trim(strtolower($keyword)));
                    }
                }
            }
        }

        return $tags;
    }
}
