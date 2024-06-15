<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content\Social;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Content\SocialMediaClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class SocialLinkedInClassifier implements Classifier
{
    const TAG_PREFIX = SocialMediaClassifier::TAG_HANDLE_PREFIX . 'linkedin:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $htmlDocument = $httpResponse->getHtmlDocument();

        $tags = [];

        if ($htmlDocument->containsAny(['href="https://www.linkedin.com/', 'href="https://de.linkedin.com/'])) {

            $tags[] = SocialMediaClassifier::TAG . 'linkedin';

            $matches = $htmlDocument->match([
                '^href="https://www.linkedin.com/company/(.*?)"^',
                '^href="https://de.linkedin.com/company/(.*?)"^',
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
