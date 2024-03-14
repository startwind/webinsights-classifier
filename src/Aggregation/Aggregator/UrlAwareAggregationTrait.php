<?php

namespace Startwind\WebInsights\Aggregation\Aggregator;

use Startwind\WebInsights\Aggregation\UrlAwareAggregationResult;

trait UrlAwareAggregationTrait
{
    const DEFAULT_SECTION = 'default';

    private array $urls = [];

    protected function addUrl(string $url, string $section = self::DEFAULT_SECTION, array $additionalData = []): void
    {
        $this->urls[$section][] = [UrlAwareAggregationResult::URL_FIELD => $url, UrlAwareAggregationResult::DATA_FIELD => $additionalData];
    }

    protected function getUrls(): array
    {
        return $this->urls;
    }
}
