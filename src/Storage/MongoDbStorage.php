<?php

namespace Startwind\WebInsights\Storage;

use MongoDB\Client;
use MongoDB\Collection;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerAwareTrait;
use Startwind\WebInsights\Application\Classification\ClassificationCommand;
use Startwind\WebInsights\Response\HttpResponse;

class MongoDbStorage implements Storage, LoggerAwareStorage, RunIdAwareStorage
{
    use LoggerAwareTrait;

    private const FIELD_RUNS = 'runs';

    const DEFAULT_MONGO_DB_PORT = 27017;

    private string $runId;

    private array $defaultOptions = [
        'server' => 'localhost',
        'port' => self::DEFAULT_MONGO_DB_PORT
    ];

    private Collection $collection;

    public function __construct(array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);

        $mongoDBUrl = "mongodb://" . $options['server'] . ':' . $options['port'];

        $client = new Client($mongoDBUrl);

        $database = $client->selectDatabase($options['database']);

        $this->collection = $database->selectCollection($options['collection']);
    }

    public function setHttpResponse(UriInterface $uri, HttpResponse $response): void
    {
        $data = $this->collection->findOne(['uri' => (string)$uri]);

        $responseArray = $response->jsonSerialize();

        $responseArray['created'] = date('Y-m-d H:i:s');

        if (!str_starts_with($this->runId, ClassificationCommand::RUN_ID_PREFIX)) {
            $responseArray[self::FIELD_RUNS] = [$this->runId];
        } else {
            $responseArray[self::FIELD_RUNS] = [];
        }

        if ($data) {
            if (property_exists($data, 'runs')) {
                $runs = json_decode(json_encode($data['runs']), true);
                $responseArray[self::FIELD_RUNS] = array_values(array_unique(array_merge($runs, $responseArray[self::FIELD_RUNS])));
            }
            try {
                $this->collection->updateOne(['_id' => $data['_id']], ['$set' => $responseArray]);
            } catch (\Exception $e) {
                $this->logger->alert('Unable to update document in MongoDB: ' . $e->getMessage());
            }
        } else {
            try {
                $this->collection->insertOne($responseArray);
            } catch (\Exception $e) {
                $this->logger->alert('Unable to insert new document in MongoDB: ' . $e->getMessage());
            }
        }
    }

    public function getHttpResponse(UriInterface $uri): HttpResponse|false
    {
        $data = $this->collection->findOne(['uri' => (string)$uri]);

        if ($data) {
            $this->updateRuns($data);
            $array = json_decode(json_encode($data, true), true);
            return HttpResponse::fromArray($array);
        } else {
            return false;
        }
    }

    private function updateRuns($data): void
    {
        if (!str_starts_with($this->runId, ClassificationCommand::RUN_ID_PREFIX)) {
            try {
                $this->collection->updateOne(['_id' => $data['_id']], ['$addToSet' => [self::FIELD_RUNS => $this->runId]]);
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'has non-array type object')) {
                    $this->logger->alert('Unable to add run id to "' . $data['_id'] . '": ' . $e->getMessage());
                    $oldRuns = json_decode(json_encode($data[self::FIELD_RUNS]), true);
                    $runs = array_values(array_unique(array_merge([$this->runId], $oldRuns)));
                    $this->collection->updateOne(['_id' => $data['_id']], ['$set' => [self::FIELD_RUNS => $runs]]);
                } else {
                    $this->logger->error('Unable to add run id to "' . $data['_id'] . '": ' . $e->getMessage());
                }
            }
        }
    }

    public function setRunId(string $runId): void
    {
        $this->runId = $runId;
    }

    public function finish(): void
    {

    }
}
