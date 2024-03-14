<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Plugin;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Exporter\Visualization;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\PluginClassifier;

class PluginAggregator extends TagCountingAggregator
{
    protected string $name = "HTML Plugins";

    protected string $description = "Those plugins are included in the HTML documents and can enrich the websites with functionality. In the last years tools like the Google Tag Manager are used to load those plugins at runtime. Those runtime-loaded plugins do not appear in this list.";

    protected string $tag = PluginClassifier::PREFIX;

    protected string $visualizationType = Visualization::TYPE_LIST_TABLE_ENRICHED;

    protected EnrichmentDictionary $dictionary;

    protected array $visualizationOptions = [
        'keyName' => 'Plugin Name (top 10)',
        'limit' => 10
    ];

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/enrichment.csv');
    }

    public function finish(): AggregationResult
    {
        $aggregationResult = parent::finish();

        $this->dictionary->handleAggregationResult($aggregationResult);
        return $aggregationResult;
    }
}
