<?php

namespace Startwind\WebInsights\Aggregation\Retriever;

use GuzzleHttp\Psr7\Uri;
use MongoDB\BSON\ObjectId;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Manager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Configuration\Exception\ConfigurationException;
use Startwind\WebInsights\Util\MongoDBHelper;

class MongoDBRetriever implements Retriever, LoggerAwareInterface
{
    use LoggerAwareTrait;

    const RUN_ID_ALL = '__all';

    private array $defaultOptions = [
        'block_size' => 5000,
        'limit' => 100000,
        'tags' => [],
        'runId' => self::RUN_ID_ALL
    ];

    private Collection $collection;

    private string $runId;

    private int $blockSize;

    private bool $endReached = false;

    private int $position = 0;

    private ?ObjectId $newestObjectId = null;

    /**
     * @var ClassificationResult[]
     */
    private array $preparedClassificationResults = [];

    private int $limit;

    private array $tags;

    private bool $firstRun = true;

    public function __construct(array $options = [])
    {
        if (!class_exists(Client::class) || !class_exists(Manager::class)) {
            throw new ConfigurationException('Unable to use MongoDBResultExporter. PHP MongoDB support not installed.');
        }

        $options = array_merge($this->defaultOptions, $options);

        $this->collection = MongoDBHelper::getCollection($options['database'], $options['collection']);

        $this->tags = $options['tags'];
        $this->runId = $options['runId'];
        $this->blockSize = $options['block_size'];
        $this->limit = $options['limit'];

        if (count($this->tags) == 0 && $this->runId === self::RUN_ID_ALL) {
            throw new ConfigurationException('At least one of the following options have to be set: tags, runId.');
        }
    }

    private function prepareClassificationResults(): void
    {
        $findQuery = [];

        if ($this->runId != self::RUN_ID_ALL) {
            $findQuery['runs'] = $this->runId;
        }

        if (count($this->tags) > 0) {
            if (count($this->tags) === 1) {
                $findQuery['tags'] = $this->tags[0];
            } else {
                $findQuery['tags'] = ['$all' => $this->tags];
            }
        }

        if (!is_null($this->newestObjectId)) {
            $findQuery['_id'] = ['$gt' => $this->newestObjectId];
        }

        $rawClassificationResults = $this->collection->find($findQuery, ['limit' => $this->blockSize, 'projection' => ['runs' => false, 'created' => false, 'domain' => false]]);

        $count = 0;

        foreach ($rawClassificationResults as $rawClassificationResult) {
            $count++;
            $this->position++;

            if ($this->position > $this->limit) continue;

            $classificationResult = new ClassificationResult(new Uri($rawClassificationResult['uri']));

            if (!property_exists($rawClassificationResult, 'tags')) {
                continue;
            }

            $tags = json_decode(json_encode($rawClassificationResult['tags']), true);

            if (is_null($tags)) continue;

            $classificationResult->addTags($tags);
            $this->newestObjectId = $rawClassificationResult['_id'];
            $this->preparedClassificationResults[] = $classificationResult;
        }

        if ($this->position > $this->limit) $this->endReached = true;
        if ($count < $this->blockSize) $this->endReached = true;

        $this->firstRun = false;
    }

    public function next(): ClassificationResult|false
    {
        if (count($this->preparedClassificationResults) < 1) {
            if ($this->endReached) return false;
            $this->prepareClassificationResults();
        }

        $next = array_pop($this->preparedClassificationResults);

        if (!$next) return $this->next();

        return $next;
    }
}
