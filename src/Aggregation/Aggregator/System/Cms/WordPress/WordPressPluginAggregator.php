<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Cms\WordPress;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Aggregation\Exporter\Visualization;
use Startwind\WebInsights\Aggregation\Util\EnrichmentDictionary;
use Startwind\WebInsights\Classification\Classifier\Cms\WordPress\WordPressPluginClassifier;

class WordPressPluginAggregator extends TagCountingAggregator
{
    protected array $visualizationOptions = [
        'keyName' => 'Plugin Name (top 10)',
        'limit' => 10
    ];

    protected string $visualizationType = Visualization::TYPE_LIST_TABLE_ENRICHED;


    protected string $name = "WordPress Plugins";

    protected string $description = "This list contains all WordPress plugins that leave their footprints in the frontend of a website. This means that this list is not complete, but already provides a good overview. If needed it is possible to do an analysis with defined backend plugins, but those have to be defined before the run.";

    protected string $tag = WordPressPluginClassifier::TAG_PREFIX;

    private EnrichmentDictionary $dictionary;

    public function __construct()
    {
        $this->dictionary = EnrichmentDictionary::fromCSVFile(__DIR__ . '/plugins_small.csv');

        $this->dictionary->add('yoast', ['name' => 'Yoast - SEO for everyone', 'homepage' => 'https://yoast.com/']);
        $this->dictionary->add('elementor-pro', ['name' => 'Elementor PRO', 'homepage' => 'https://elementor.com/']);
        $this->dictionary->add('revslider', ['name' => 'Slider Revolution', 'homepage' => 'https://www.sliderrevolution.com/']);
        $this->dictionary->add('borlabs-cookie', ['name' => 'Borlabs Cookie Solution', 'homepage' => 'https://de.borlabs.io/borlabs-cookie/']);
        $this->dictionary->add('wp-rocket', ['name' => 'WP Rocket - Superior WordPress Performance', 'homepage' => 'https://wp-rocket.me/']);
        $this->dictionary->add('js_composer', ['name' => 'WPBakery Page Builder (js_composer)', 'homepage' => 'https://wpbakery.com/']);
        $this->dictionary->add('gravityforms', ['name' => 'Gravity Forms', 'homepage' => 'https://www.gravityforms.com/']);
        $this->dictionary->add('sitepress-multilingual-cms', ['name' => 'WPML - WordPress Multilingual Plugin', 'homepage' => 'https://wpml.org/']);
        $this->dictionary->add('bluehost-wordpress-plugin', ['name' => 'Bluehost WordPress Plugin', 'homepage' => '']);
        $this->dictionary->add('elementor', ['name' => 'Elementor Website Builder', 'homepage' => 'https://elementor.com/']);
    }

    public function finish(): AggregationResult
    {
        return $this->dictionary->handleAggregationResult(parent::finish());
    }
}
