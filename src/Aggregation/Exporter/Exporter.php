<?php

namespace Startwind\WebInsights\Aggregation\Exporter;

use Startwind\WebInsights\Aggregation\AggregationResult;

interface Exporter
{
    public function export(AggregationResult $aggregationResult): void;

    public function finish(int $numberOfProcessedWebsites): void;
}
