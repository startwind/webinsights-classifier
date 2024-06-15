<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content\Social;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Content\SocialMediaClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class SocialFacebookClassifier implements Classifier
{
    const TAG_PREFIX = SocialMediaClassifier::TAG_HANDLE_PREFIX . 'facebook:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $htmlDocument = $httpResponse->getHtmlDocument();

        $tags = [];

        if ($htmlDocument->containsAny([
                'href="https://www.facebook.com/',
                'href="//facebook.com/'
            ]
        )) {

            $tags[] = SocialMediaClassifier::TAG . 'facebook';

            $matches = $htmlDocument->match([
                '^href="https://www.facebook.com/(.*?)"^',
                '^href="https://facebook.com/(.*?)"^',
                '^href="//facebook.com/(.*?)"^'
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
