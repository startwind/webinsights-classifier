<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Technology;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Http\Http\WebServerClassifier;

class WebserverAggregator extends TagCountingAggregator
{
    protected string $name = "Web Server Distribution";

    protected string $description = "The webserver handles the HTTP(S) requests and connected them with the backend programming language.";

    protected string $tag = WebServerClassifier::TAG_PREFIX;

    private EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/webserver_enrichment.csv');
    }

    public function finish(): AggregationResult
    {
        return $this->dictionary->handleAggregationResult(parent::finish());
    }
}
