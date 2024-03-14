<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\General;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\Aggregator;
use Startwind\WebInsights\Aggregation\Aggregator\UrlAwareAggregationTrait;
use Startwind\WebInsights\Aggregation\UrlAwareAggregationResult;
use Startwind\WebInsights\Classification\ClassificationResult;

class RawDataAggregator implements Aggregator
{
    use UrlAwareAggregationTrait;

    public const RAW_URLS = 'urls';

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $this->addUrl($classificationResult->getUri(), self::DEFAULT_SECTION, ['tags' => implode(', ', $classificationResult->getTags())]);
        $this->addUrl($classificationResult->getUri(), self::RAW_URLS);
    }

    public function finish(): AggregationResult
    {
        $result = new UrlAwareAggregationResult([], '', '', self::class);

        $result->setUrls($this->getUrls());

        $result->setVisualizationType('special_raw_data');
        $result->setVisualizationOptions(['headline' => 'Domains with tags', 'description' => 'This CSV file contains all domains that where part of this analysis.']);

        return $result;
    }
}
