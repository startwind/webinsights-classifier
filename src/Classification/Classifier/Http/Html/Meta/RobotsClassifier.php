<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Meta;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class RobotsClassifier implements Classifier
{
    private const PREFIX = 'html:meta:robots:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $dom = $httpResponse->getHtmlDocument()->asDomDocument();

        $tags = [];

        foreach ($dom->getElementsByTagName('meta') as $tag) {
            if ($tag->getAttribute('name') === 'robots') {
                foreach (explode(',', $tag->getAttribute('content')) as $keyword) {
                    $tags[] = self::PREFIX . str_replace(' ', '_', trim(strtolower($keyword)));
                }
            }
        }

        return $tags;
    }
}
