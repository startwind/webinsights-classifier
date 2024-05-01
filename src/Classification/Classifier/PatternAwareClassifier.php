<?php

namespace Startwind\WebInsights\Classification\Classifier;

use Startwind\WebInsights\Response\HttpResponse;

abstract class PatternAwareClassifier
{
    protected const TAG_PREFIX = '';

    protected const SOURCE_HTML = 'html';

    protected array $keywords = [];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $tags = [];

        if (array_key_exists(self::SOURCE_HTML, $this->keywords)) {
            $keywords = $this->keywords[self::SOURCE_HTML];
            foreach ($keywords as $key => $keyword) {
                if (!is_array($keyword)) {
                    $keyword = [$keyword];
                }

                foreach ($keyword as $singleKeyword) {
                    if ($httpResponse->getHtmlDocument()->contains($singleKeyword)) {
                        $tags[] = static::TAG_PREFIX . $key;
                    }
                }
            }
        }

        return $tags;
    }
}
