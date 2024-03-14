<?php

namespace Startwind\WebInsights\Aggregation\Exporter;

use Startwind\WebInsights\Aggregation\AggregationResult;

abstract class FinishExporter implements Exporter
{
    /**
     * @var AggregationResult[]
     */
    protected array $aggregationResults;

    public function export(AggregationResult $aggregationResult): void
    {
        $this->aggregationResults[] = $aggregationResult;
    }
}
