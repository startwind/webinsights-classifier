<?php

namespace Startwind\WebInsights\Classification\Classifier\Security\Hacked;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class HackedKeywordsClassifier implements Classifier
{
    const TAG_PREFIX = 'security:hacked:keywords:';

    private array $keywords = [
        'porn ', 'p0rn ',
        'casino', 'cas1no',
        'sex'
    ];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $tags = [];

        foreach ($this->keywords as $keyword) {
            if ($httpResponse->getHtmlDocument()->contains($keyword)) {
               $tags[] = self::TAG_PREFIX. $keyword;
            }
        }

        return $tags;
    }
}
