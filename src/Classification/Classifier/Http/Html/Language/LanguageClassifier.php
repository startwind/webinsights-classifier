<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Language;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Helper\XPathHelper;
use Startwind\WebInsights\Response\HttpResponse;

class LanguageClassifier implements Classifier
{
    public const TAG_PREFIX = 'html:content:language:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $matches = $httpResponse->getHtmlDocument()->match('/hreflang="(.*?)"/');

        $tags = [];

        foreach ($matches as $match) {
            if ($match == 'x-default') continue;
            if (strlen($match) > 10) continue;
            $tags[] = self::TAG_PREFIX . strtolower($match);
        }

        $matches = $httpResponse->getHtmlDocument()->match('/ lang="(.*?)"/');

        if (array_key_exists(1, $matches)) {
            $tags[] = self::TAG_PREFIX . strtolower($matches[1]);
        }

        $ogTag = XPathHelper::value($httpResponse->getHtmlDocument(), '//meta[@property="og:locale"]/@content');

        if ($ogTag) {
            $tags[] = self::TAG_PREFIX . $ogTag;
        }

        return $tags;
    }
}
