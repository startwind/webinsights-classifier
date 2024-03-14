<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Keyword;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class KeywordClassifier extends HtmlClassifier
{
    public const PREFIX = 'keyword:';

    private array $keywords;

    public function init($parameters)
    {
        $this->keywords = $parameters['keywords'];
    }

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        foreach ($this->keywords as $keyword) {
            if ($htmlDocument->contains($keyword)) {
                $tags[] = self::PREFIX . str_replace(' ', '_', strtolower($keyword));
            }
        }

        return $tags;
    }
}
