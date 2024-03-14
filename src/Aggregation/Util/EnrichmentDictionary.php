<?php

namespace Startwind\WebInsights\Aggregation\Util;

use Startwind\WebInsights\Aggregation\AggregationResult;

class EnrichmentDictionary
{
    private array $enrichmentData;

    public function __construct(array $enrichmentData = [])
    {
        $this->enrichmentData = $enrichmentData;
    }

    public function add(string $key, array $value): void
    {
        $this->enrichmentData[trim($key)] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists(trim($key), $this->enrichmentData);
    }

    public function get(string $key): array
    {
        return $this->enrichmentData[trim($key)];
    }

    public function handleAggregationResult(AggregationResult $aggregationResult): AggregationResult
    {
        $enrichment = $aggregationResult->getEnrichment();

        foreach ($aggregationResult->getResults() as $key => $result) {
            if ($this->has($key)) {
                $enrichment->add($key, $this->get($key));
            }
        }

        return $aggregationResult;
    }

    public static function fromCSVFile(string $csvFilename): self
    {
        $dictionary = new self();

        $handle = fopen($csvFilename, 'r');

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (is_null($data[0])) {
                throw new \RuntimeException('Unable to parse "' . $csvFilename . '". Seems like there is an empty line in the document.');
            }

            if (!array_key_exists(2, $data)) {
                throw new \RuntimeException('Unable to parse "'.$data[0].'" in "' . $csvFilename . '". Seems like an element is missing in that row.');
            }

            $value = [
                'name' => strlen($data[1]) > 60 ? substr($data[1], 0, 60) . "..." : $data[1],
                'homepage' => $data[2]
            ];
            $dictionary->add($data[0], $value);
        }

        return $dictionary;
    }
}
