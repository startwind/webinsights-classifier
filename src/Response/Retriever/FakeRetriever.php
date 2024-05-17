<?php

namespace Startwind\WebInsights\Response\Retriever;

use GuzzleHttp\Psr7\Uri;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Startwind\WebInsights\Response\HttpResponse;

class FakeRetriever implements Retriever, LoggerAwareRetriever
{
    /**
     * @var \Psr\Http\Message\UriInterface[]
     */
    private array $uris;

    private LoggerInterface $logger;

    private int $position = 0;

    private int $limit = 0;

    public function __construct(array $options = [])
    {
        $this->logger = new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function setUris(array $uris): void
    {
        $this->uris = $uris;
        $this->limit = count($uris);
    }

    /**
     * @inheritDoc
     */
    public function next(): HttpResponse|false
    {
        if ($this->position < $this->limit) {
            $response = new HttpResponse(
                '',
                [],
                200,
                new Uri($this->uris[$this->position]),
                0,
                '0.0.0.0'
            );

            $this->position++;

            return $response;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        $this->logger->info('Added logger "' . get_class($logger) . '" to GuzzleRetriever.');
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return $this->position - 1;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
