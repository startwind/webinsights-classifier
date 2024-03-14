<?php

namespace Startwind\WebInsights\Aggregation;

class UrlAwareAggregationResult extends AggregationResult
{
    public const URL_FIELD = 'url';
    public const DATA_FIELD = 'data';

    private array $urls = [];

    public function setUrls(array $urls): void
    {
        $this->urls = $urls;
    }

    public function getUrls(): array
    {
        return $this->urls;
    }

    public static function fromAggregationResult(AggregationResult $aggregationResult, array $urls = []): self
    {
        $urlAwareResult = new self($aggregationResult->getResults(), $aggregationResult->getDescription(), $aggregationResult->getName(), $aggregationResult->getGenerator());

        if ($urls) {
            $urlAwareResult->setUrls($urls);
        }

        return $urlAwareResult;
    }
}
