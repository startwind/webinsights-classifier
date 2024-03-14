<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Hosting;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Exporter\Visualization;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Url\IPLocationClassifier;

class IspAggregator extends TagCountingAggregator
{
    protected string $name = "Internet Service Provider (ISP) Distribution";

    protected string $description = "The ISP gets derived from the IP address of the website. If a CDN is in front of the website the CDN will occur in this list.";

    protected string $tag = IPLocationClassifier::TAG_HOSTING_LOCATION_ISP_PREFIX;

    protected array $visualizationOptions = [
        'keyName' => 'ISP (top 10)',
        'limit' => 10
    ];

    protected bool $dissolveUnderscore = true;

    protected string $visualizationType = Visualization::TYPE_LIST_TABLE_ENRICHED;

    protected EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/isp_enrichment.csv');
    }

    public function finish(): AggregationResult
    {
        $aggregationResult = parent::finish();
        $this->dictionary->handleAggregationResult($aggregationResult);
        return $aggregationResult;
    }
}
