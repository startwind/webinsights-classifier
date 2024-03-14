<?php

namespace Startwind\WebInsights\Aggregation;

class AggregationResultEnrichment implements \JsonSerializable
{
    protected array $enrichmentData;

    public function __construct(array $enrichmentData = [])
    {
        $this->enrichmentData = $enrichmentData;
    }

    public function add(string $key, array $value): void
    {
        $this->enrichmentData[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->enrichmentData);
    }

    public function get($key): array
    {
        return $this->enrichmentData[$key];
    }

    public function jsonSerialize(): array
    {
        return [
            'enrichmentData' => $this->enrichmentData
        ];
    }
}
