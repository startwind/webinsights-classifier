<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Content;

use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Meta\KeywordClassifier;

class MetaKeywordAggregator extends TagCountingAggregator
{
    protected string $name = "HTML Meta Keywords";

    protected string $description = "This lists includes all meta keywords found in the HTML document. As Google does not process this data anymore the number of websites that use this feature decreases.";

    protected string $tag = KeywordClassifier::PREFIX;

    protected bool $dissolveUnderscore = true;
}
