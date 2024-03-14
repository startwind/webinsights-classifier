<?php

namespace Startwind\WebInsights\Response\Retriever;

use GuzzleHttp\Client;

interface HttpClientAwareRetriever
{
    /**
     * Set the Guzzle HTTP client
     */
    public function setHttpClient(Client $client): void;
}
