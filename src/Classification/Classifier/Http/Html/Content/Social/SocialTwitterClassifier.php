<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content\Social;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Content\SocialMediaClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class SocialTwitterClassifier implements Classifier
{
    const TAG_PREFIX = SocialMediaClassifier::TAG_HANDLE_PREFIX . 'twitter:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $htmlDocument = $httpResponse->getHtmlDocument();

        $tags = [];

        if ($htmlDocument->containsAny(['href="https://twitter.com/', 'href="https://www.twitter.com/', 'href="//twitter.com/'])) {

            $tags[] = SocialMediaClassifier::TAG . 'twitter';

            $matches = $htmlDocument->match([
                '^href="https://twitter.com/(.*?)"^',
                '^href="https://www.twitter.com/(.*?)"^',
                '^href="//twitter.com/(.*?)"^',
            ]);

            foreach ($matches as $match) {
                if (strlen($match) < 20 && !str_contains($match, '/')) {
                    if ($match != '') {
                        $tags[] = self::TAG_PREFIX . $match;
                    }
                }
            }
        }

        return $tags;
    }
}
