<?php

namespace Startwind\WebInsights\Application\Aggregation;

use Startwind\WebInsights\Aggregation\Configuration\AggregationConfiguration;
use Startwind\WebInsights\Util\Timer;
use Symfony\Component\Console\Command\Command;

abstract class AggregationCommand extends Command
{
    protected const OPTION_CONFIG_FILE = 'configFile';

    protected AggregationConfiguration $configuration;

    protected function doExecute(): int
    {
        $retriever = $this->configuration->getRetriever();

        $aggregators = $this->configuration->getAggregators();

        $count = 0;

        $timer = new Timer();

        while ($classificationResult = $retriever->next()) {
            $count++;
            foreach ($aggregators as $aggregator) {
                $timer->start();
                $aggregator->aggregate($classificationResult);
                $time = $timer->getTimePassed();
                if ($time > 50) {
                    $this->configuration->getLogger()->warning('Aggregation was slow. Aggregator: ' . get_class($aggregator) . ', time: ' . $time . 'ms.');
                }
            }
        }

        if ($count === 0) {
            throw new \RuntimeException('The given builder query did not return any results.');
        }

        $exporter = $this->configuration->getExporter();

        foreach ($aggregators as $aggregator) {
            $exporter->export($aggregator->finish());
        }

        $exporter->finish($count);

        $this->configuration->getLogger()->info('Aggregation finished. Memory usage: ' . (int)(memory_get_peak_usage() / 1024 / 1024) . ' MB.');

        return $count;
    }
}
