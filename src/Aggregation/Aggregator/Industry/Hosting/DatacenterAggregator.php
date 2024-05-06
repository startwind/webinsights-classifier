<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Industry\Hosting;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Url\IPLocationClassifier;

class DatacenterAggregator extends CountingAggregator
{
    protected int $limit = 30;

    private EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/datacenter_enrichment.csv');
    }

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $as = $classificationResult->getTagsStartingWithString(IPLocationClassifier::TAG_HOSTING_LOCATION_PREFIX . 'as:', true, true);

        if (count($as) === 1 && !str_contains($as[0], 'cloudflare')) {
            $this->increaseCount($as[0]);
        }
    }

    public function finish(): AggregationResult
    {
        return $this->dictionary->handleAggregationResult(parent::finish());
    }
}
