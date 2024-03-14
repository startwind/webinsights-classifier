<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Technology\SSLCertificate;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Exporter\Visualization;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Http\Http\SSLCertificateClassifier;

class SSLCertificateIssuerAggregator extends TagCountingAggregator
{
    protected string $name = "SSL Issuer Distribution";
    protected string $description = "An SSL (Secure Sockets Layer) issuer, often referred to as a Certificate Authority (CA), is an entity that issues digital certificates for use in securing websites and online communication.";

    protected string $tag = SSLCertificateClassifier::TAG_PREFIX_ISSUER;

    protected array $visualizationOptions = [
        'keyName' => 'Issuer (top 10)',
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
