<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Service\EmailService;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Exporter\Visualization;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Service\Email\EmailServiceClassifier;

class EmailServiceAggregator extends TagCountingAggregator
{
    protected string $name = "E-Mail Service Distribution";
    protected string $description = "The email service provider is derived from the mx entry in the websites DNS settings. If a website has a self hosted email service provider it means that the mx entry points to the website itself.";
    protected string $unknown = "&lt;none&gt;";
    protected bool $addUnknownTag = true;
    protected string $tag = EmailServiceClassifier::TAG_PREFIX;

    protected string $visualizationType = Visualization::TYPE_LIST_TABLE_ENRICHED;
    protected array $visualizationOptions = [
        'keyName' => 'Service (top 10)',
        'limit' => 10
    ];


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
