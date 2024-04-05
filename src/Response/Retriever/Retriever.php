<?php

namespace Startwind\WebInsights\Response\Retriever;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Configuration\Initializable;
use Startwind\WebInsights\Response\HttpResponse;

interface Retriever extends Initializable
{
    public const LIMIT_UNLIMITED = -1;

    public function __construct(array $options = []);

    public function setLimit(int $limit): void;

    public function setPosition(int $position): void;

    /**
     * Set the uris that have to be visited.
     *
     * @var UriInterface[] $uris
     */
    public function setUris(array $uris): void;

    /**
     * Get the next HTTP response. Returns false if all uris were already
     * visited.
     */
    public function next(): HttpResponse|false;

    /**
     * Return the position of the last returned response.
     */
    public function getPosition(): int;
}
