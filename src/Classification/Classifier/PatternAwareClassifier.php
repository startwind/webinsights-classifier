<?php

namespace Startwind\WebInsights\Classification\Classifier;

use Startwind\WebInsights\Response\HttpResponse;

abstract class PatternAwareClassifier
{
    protected bool $treeStructure = false;

    protected const TAG_PREFIX = '';

    protected const SOURCE_HTML = 'html';
    protected const SOURCE_BODY = 'body';

    protected array $keywords = [];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $tags = [];

        foreach ($this->keywords as $type => $keywords) {
            foreach ($keywords as $key => $keyword) {
                if (!is_array($keyword)) {
                    $keyword = [$keyword];
                }

                foreach ($keyword as $index => $singleKeyword) {
                    if (strlen($singleKeyword) < 4) {
                        $keyword[] = ' ' . $singleKeyword;
                        $keyword[] = $singleKeyword . ' ';
                        $keyword[] = $singleKeyword . '. ';
                        unset($keyword[$index]);
                    }
                }

                foreach ($keyword as $singleKeyword) {
                    $found = false;

                    switch ($type) {
                        case self::SOURCE_HTML:
                            if ($httpResponse->getHtmlDocument()->contains($singleKeyword)) $found = true;
                            break;
                        case self::SOURCE_BODY:
                            if (str_contains(strtolower($httpResponse->getHtmlDocument()->getBody(true)), $singleKeyword)) {
                                $found = true;
                            }
                            break;
                    }

                    if ($found) {
                        $tags[] = static::TAG_PREFIX . $key;
                    }
                }
            }
        }

        if ($this->treeStructure) {
            foreach ($tags as $tag) {
                $suffix = str_replace(static::TAG_PREFIX, '', $tag);

                $parts = explode(':', $suffix);

                if (count($parts) === 2) {
                    $tags[] = static::TAG_PREFIX . $parts[0];
                }
            }
        }

        return $tags;
    }
}
