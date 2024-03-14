<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Ecommerce\EcommerceClassifier;

class EcommerceAggregator extends TagCountingAggregator
{
    protected bool $addUnknownTag = false;

    protected string $name = "E-Commerce System Distribution";

    protected string $description = "We recognise the E-Commerce system on the basis of signatures that such applications leave in HTML or HTTP. As some websites try to hide such features for security reasons, it is not possible to find suitable systems for all websites.";

    protected string $tag = EcommerceClassifier::TAG_ECOMMERCE_SYSTEM_PREFIX;

    private EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/ecommerce_enrichment.csv');
    }

    public function finish(): AggregationResult
    {
        return $this->dictionary->handleAggregationResult(parent::finish());
    }
}

