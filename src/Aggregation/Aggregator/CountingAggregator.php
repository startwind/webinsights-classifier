<?php

namespace Startwind\WebInsights\Aggregation\Aggregator;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Exporter\Visualization;

abstract class CountingAggregator extends SimpleAggregator
{
    protected const POOL_DEFAULT = 'default';

    protected int $limit = 10;

    private array $countable = [];

    protected string $visualizationType = Visualization::TYPE_LIST_TABLE;
    protected array $visualizationOptions = [];

    protected function increaseCount(string|array $keys, $pool = self::POOL_DEFAULT): void
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $key) {
            if (!array_key_exists($pool, $this->countable)) {
                $this->countable[$pool] = [];
            }

            if (array_key_exists($key, $this->countable[$pool])) {
                $this->countable[$pool][$key]++;
            } else {
                $this->countable[$pool][$key] = 1;
            }
        }
    }

    protected function getCount($pool = self::POOL_DEFAULT): array
    {
        if (array_key_exists($pool, $this->countable)) {
            return $this->countable[$pool];
        } else {
            return [];
        }
    }

    protected function getCounts(): array
    {
        return $this->countable;
    }

    public function finish(): AggregationResult
    {
        $countable = $this->getCounts();

        if (count($countable) === 1) {
            if (array_key_first($countable) === self::POOL_DEFAULT) {
                $results = $countable[self::POOL_DEFAULT];
                if ($this->limit !== -1) {
                    arsort($results);
                    $results = array_slice($results, 0, $this->limit);
                }
                $result = new AggregationResult($results, $this->getDescription(), $this->getName(), get_class($this));
                $result->setVisualizationType($this->visualizationType);
                $result->setVisualizationOptions($this->visualizationOptions);

                return $result;
            }
        }

        $result = new AggregationResult($countable, $this->getDescription(), $this->getName(), get_class($this));
        $result->setVisualizationType($this->visualizationType);
        $result->setVisualizationOptions($this->visualizationOptions);

        return $result;
    }
}
