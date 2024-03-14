<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Cms;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Cms\CmsClassifier;

class CmsAggregator extends TagCountingAggregator
{
    protected string $name = "CMS Distribution";

    protected string $description = "CMS stands for Content Management System, and it refers to software applications or platforms that enable users to create, manage, and modify digital content without requiring advanced technical skills. CMS is widely used for websites and online platforms to simplify the process of content creation and maintenance.";

    protected string $tag = CmsClassifier::CLASSIFIER_PREFIX;

    private EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/cms_enrichment.csv');
    }

    public function finish(): AggregationResult
    {
        return $this->dictionary->handleAggregationResult(parent::finish());
    }
}
