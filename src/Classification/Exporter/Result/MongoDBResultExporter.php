<?php

namespace Startwind\WebInsights\Classification\Exporter\Result;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Manager;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Exporter\Exporter;
use Startwind\WebInsights\Configuration\Exception\ConfigurationException;

class MongoDBResultExporter implements Exporter
{
    private Collection $collection;

    private const FIELD_RUNS = 'runs';

    private const DEFAULT_MONGO_DB_PORT = 27017;

    private string $runId;

    private array $defaultOptions = [
        'server' => 'localhost',
        'port' => self::DEFAULT_MONGO_DB_PORT
    ];

    public function __construct(string $runId, array $options)
    {
        if (!class_exists(Client::class) || !class_exists(Manager::class)) {
            throw new ConfigurationException('Unable to use MongoDBResultExporter. PHP MongoDB support not installed.');
        }

        $options = array_merge($this->defaultOptions, $options);

        $mongoDBUrl = "mongodb://" . $options['server'] . ':' . $options['port'];

        $client = new Client($mongoDBUrl);

        $database = $client->selectDatabase($options['database']);

        $this->collection = $database->selectCollection($options['collection']);

        $this->runId = $runId;
    }

    public function export(ClassificationResult $classificationResult): void
    {
        $data = $this->collection->findOne(['uri' => (string)$classificationResult->getUri()]);

        $responseArray['uri'] = (string)$classificationResult->getUri();
        $responseArray['created'] = date('Y-m-d H:i:s');
        $responseArray['tags'] = array_values($classificationResult->getTags());

        if ($data) {
            if (property_exists($data, self::FIELD_RUNS)) {
                $oldRuns = json_decode(json_encode($data[self::FIELD_RUNS]), true);
                $responseArray[self::FIELD_RUNS] = array_values(array_unique(array_merge([$this->runId], $oldRuns)));
            } else {
                $responseArray[self::FIELD_RUNS] = [$this->runId];
            }

            $this->collection->updateOne(['_id' => $data['_id']], ['$set' => $responseArray]);
        } else {
            $responseArray[self::FIELD_RUNS] = [$this->runId];
            $this->collection->insertOne($responseArray);
        }
    }

    public function finish(): string
    {
        return "";
    }
}
