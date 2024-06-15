<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class SocialMediaClassifier extends HtmlClassifier
{
    public const TAG = 'html:content:link:social-media:';

    public const TAG_HANDLE_PREFIX = 'social-media:';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        // for Twitter see SocialTwitterClassifier

        // for Facebook see SocialFacebookClassifier

        // for LinkedIn see SocialLinkedInClassifier


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

        if ($htmlDocument->containsAny(['https://mastodon.social/'])) {
            $tags[] = self::TAG . 'mastodon';
        }

        return $tags;
    }
}
