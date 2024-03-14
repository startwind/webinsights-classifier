<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Technology\CDN;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Exporter\Visualization;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Http\Http\Cdn\CDNClassifier;

class CDNAggregator extends TagCountingAggregator
{
    protected string $name = "CDN Service Distribution";

    protected string $description = "The CDN information is derived from IP address and HTTP header information. If a website is behind a CDN further website classification can be harder as the CDN normally overwrites some server information.";

    protected string $tag = CDNClassifier::TAG_PREFIX;

    protected array $visualizationOptions = [
        'keyName' => 'CDN (top 10)',
        'limit' => 10
    ];

    protected string $visualizationType = Visualization::TYPE_LIST_TABLE_ENRICHED;

    private EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/enrichment.csv');
    }

    public function finish(): AggregationResult
    {
        return $this->dictionary->handleAggregationResult(parent::finish());
    }
}
