<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\General;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\Aggregator;
use Startwind\WebInsights\Classification\ClassificationResult;

class GeneralOverviewAggregator implements Aggregator
{
    public function aggregate(ClassificationResult $classificationResult): void
    {
        // TODO: Implement aggregate() method.
    }

    public function finish(): AggregationResult
    {
        return new AggregationResult(
            [],
            '',
            '',
            self::class
        );
    }

}
