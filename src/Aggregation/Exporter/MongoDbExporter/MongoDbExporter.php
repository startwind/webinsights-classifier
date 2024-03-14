<?php

namespace Startwind\WebInsights\Aggregation\Exporter\MongoDbExporter;

use MongoDB\Collection;
use Startwind\WebInsights\Aggregation\Exporter\FinishExporter;
use Startwind\WebInsights\Aggregation\UrlAwareAggregationResult;
use Startwind\WebInsights\Util\FilenameHelper;
use Startwind\WebInsights\Util\MongoDBHelper;
use Startwind\WebInsights\Util\StorageHelper;

class MongoDbExporter extends FinishExporter
{
    private string $uuid;
    private Collection $collection;

    private array $defaultOptions = [
        'outputDirectory' => '_results/report/default/',
    ];

    private string $outputDirectory;

    public function __construct(array $options = [])
    {
        $this->uuid = $options['uuid'];

        $options = array_merge($this->defaultOptions, $options);

        $this->collection = MongoDBHelper::getCollection($options['database'], $options['collection']);

        $this->outputDirectory = FilenameHelper::process($options['outputDirectory']);
    }

    public function finish(int $numberOfProcessedWebsites): void
    {
        $document = [
            'uuid' => $this->uuid,
            'created' => date('Y-m-d H:i:s'),
            'processedWebsites' => $numberOfProcessedWebsites,
            'results' => [],
        ];

        foreach ($this->aggregationResults as $aggregationResult) {
            $document['results'][$aggregationResult->getGenerator()] = $aggregationResult->jsonSerialize();

            if ($aggregationResult instanceof UrlAwareAggregationResult) {
                $files = StorageHelper::store($aggregationResult, $this->outputDirectory);
                $document['results'][$aggregationResult->getGenerator()]['files'] = $files;
            }
        }

        $this->collection->insertOne($document);
    }
}
