<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Technology;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;

class ProgrammingLanguageAggregator extends TagCountingAggregator
{
    protected string $name = "Backend Programming Language Distribution";

    protected string $description = "The backend programming language is responsible to create the HTML document in classic system. The language can be found in HTTP headers and can also be derived from the CMS or e-commerce system.";

    protected string $tag = ProgrammingLanguageClassifier::TAG_PREFIX;

    private EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/programming_enrichment.csv');
    }

    public function finish(): AggregationResult
    {
        return $this->dictionary->handleAggregationResult(parent::finish());
    }
}
