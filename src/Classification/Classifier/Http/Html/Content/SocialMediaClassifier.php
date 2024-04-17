<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SocialMediaClassifier extends HtmlClassifier
{
    const TAG = 'html:content:link:social-media:';
    const TWITTER_TAG = 'social-media:twitter:';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        if ($htmlDocument->containsAny(['href="https://www.facebook.com/'])) {
            $tags[] = self::TAG . 'facebook';
        }

        if ($htmlDocument->containsAny(['href="https://twitter.com/', 'href="https://www.twitter.com/'])) {
            $tags[] = self::TAG . 'twitter';

            $matches = $htmlDocument->match([
                '^href="https://twitter.com/(.*?)"^',
                '^href="https://www.twitter.com/(.*?)"^'
            ]);

            foreach ($matches as $match) {
                if (strlen($match) < 20 && !str_contains($match, '/')) {
                    $tags[] = self::TWITTER_TAG . $match;
                }
            }
        }

        if ($htmlDocument->containsAny(['href="https://www.linkedin.com/', 'href="https://de.linkedin.com/'])) {
            $tags[] = self::TAG . 'linkedin';
        }

        if ($htmlDocument->containsAny(['href="https://www.youtube.com/'])) {
            $tags[] = self::TAG . 'youtube';
        }

        if ($htmlDocument->containsAny(['href="https://www.xing.com/companies'])) {
            $tags[] = self::TAG . 'xing';
        }

        if ($htmlDocument->containsAny(['href="https://www.instagram.com'])) {
            $tags[] = self::TAG . 'instagram';
        }

        if ($htmlDocument->containsAny(['href="https://plus.google.com/'])) {
            $tags[] = self::TAG . 'google-plus';
        }

        if ($htmlDocument->containsAny(['href="https://www.tiktok.com'])) {
            $tags[] = self::TAG . 'tiktok';
        }

        return $tags;
    }
}
