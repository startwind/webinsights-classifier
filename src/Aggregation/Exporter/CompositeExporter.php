<?php

namespace Startwind\WebInsights\Aggregation\Exporter;

use Startwind\WebInsights\Aggregation\AggregationResult;

class CompositeExporter implements Exporter
{
    /**
     * @var \Startwind\WebInsights\Aggregation\Exporter\Exporter[]
     */
    private array $exporters = [];

    public function addExporter(Exporter $exporter): void
    {
        $this->exporters[] = $exporter;
    }

    public function export(AggregationResult $aggregationResult): void
    {
        foreach ($this->exporters as $exporter) {
            $exporter->export($aggregationResult);
        }
    }

    public function finish(int $numberOfProcessedWebsites): void
    {
        foreach ($this->exporters as $exporter) {
            $exporter->finish($numberOfProcessedWebsites);
        }
    }
}
