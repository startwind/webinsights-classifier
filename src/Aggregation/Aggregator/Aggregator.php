<?php

namespace Startwind\WebInsights\Aggregation\Aggregator;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Classification\ClassificationResult;

interface Aggregator
{
    public function aggregate(ClassificationResult $classificationResult): void;

    public function finish(): AggregationResult;
}
