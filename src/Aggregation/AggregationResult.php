<?php

namespace Startwind\WebInsights\Aggregation;

class AggregationResult implements \JsonSerializable
{
    private array $results;

    private string $description;

    private string $generator;
    private string $name;

    protected string $visualizationType = '';
    protected array $visualizationOptions = [];

    private AggregationResultEnrichment $enrichment;

    public function __construct(array $results, string $description, string $name, string $generator)
    {
        $this->results = $results;
        $this->description = $description;
        $this->generator = $generator;
        $this->name = $name;

        $this->enrichment = new AggregationResultEnrichment();
    }

    public function getResults(bool $sortByCount = true): array
    {
        $results = $this->results;
        if ($sortByCount) {
            arsort($results);
        }
        return $results;
    }

    public function getResultAsRow(?string $key = null): array
    {
        if ($this->hasMultipleResults()) {
            if (is_null($key)) {
                throw new \RuntimeException('As the given aggregation result has multiple results a key is mandatory');
            }

            $results = $this->results[$key];
        } else {
            $results = $this->getResults();
        }

        $rows = [];

        foreach ($results as $key => $value) {
            $rows[] = [(string)$key, $value];
        }

        return $rows;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function hasMultipleResults(): bool
    {
        if (count($this->results) === 0) return false;
        $firstElement = $this->results[array_key_first($this->results)];
        return is_array($firstElement);
    }

    public function hasResults(): bool
    {
        return count($this->results) > 0;
    }

    public function getGenerator(): string
    {
        return $this->generator;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    public function getVisualizationType(): string
    {
        return $this->visualizationType;
    }

    public function setVisualizationType(string $type): void
    {
        $this->visualizationType = $type;
    }

    public function setVisualizationOptions(array $options): void
    {
        $this->visualizationOptions = $options;
    }

    public function getVisualizationOptions(): array
    {
        return $this->visualizationOptions;
    }

    public function isVisualizable(): bool
    {
        return $this->visualizationType != '';
    }

    public function setEnrichment(AggregationResultEnrichment $enrichment): void
    {
        $this->enrichment = $enrichment;
    }

    public function getEnrichment(): AggregationResultEnrichment
    {
        return $this->enrichment;
    }

    public function jsonSerialize(): array
    {
        return [
            'generator' => $this->getGenerator(),
            'name' => $this->getName(),
            'results' => $this->getResults(),
            'enrichment' => $this->getEnrichment()->jsonSerialize(),
            'visualization' => [
                'type' => $this->getVisualizationType()
            ]
        ];
    }
}
