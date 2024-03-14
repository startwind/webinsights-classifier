<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Domain;

use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Classification\Classifier\Url\TopLevelDomainClassifier;

class TopLevelDomainAggregator extends TagCountingAggregator
{
    protected string $name = "Top Level Domain Distribution";

    protected string $description = "This list contains the distribution of the top level domains. ";

    protected string $tag = TopLevelDomainClassifier::CLASSIFIER_PREFIX;
}
