<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Cms\WordPress;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Exporter\Visualization;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Cms\WordPress\WordPressPluginClassifier;

class WordPressThemeAggregator extends TagCountingAggregator
{
    protected array $visualizationOptions = [
        'keyName' => 'Plugin Name (top 10)',
        'limit' => 10
    ];

    protected string $visualizationType = Visualization::TYPE_LIST_TABLE_ENRICHED;


    protected string $name = "WordPress Themes";

    protected string $description = "This list contains all WordPress plugins that leave their footprints in the frontend of a website. This means that this list is not complete, but already provides a good overview. If needed it is possible to do an analysis with defined backend plugins, but those have to be defined before the run.";

    protected string $tag = WordPressPluginClassifier::TAG_PREFIX;

    private EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/themes.csv');
    }

    public function finish(): AggregationResult
    {
        return $this->dictionary->handleAggregationResult(parent::finish());
    }
}
