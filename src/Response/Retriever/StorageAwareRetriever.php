<?php

namespace Startwind\WebInsights\Response\Retriever;

use Startwind\WebInsights\Storage\Storage;

interface StorageAwareRetriever
{
    /**
     * Set the storage that persists HTTP responses.
     */
    public function setStorage(Storage $storage): void;
}
