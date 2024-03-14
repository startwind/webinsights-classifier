<?php

namespace Startwind\WebInsights\Storage;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Response\HttpResponse;

class ApiStorage implements Storage
{
    private int $responsesCached = 0;
    private int $responsesNotCached = 0;

    private array $defaultOptions = [
        'setEndpoint' => 'https://api.webinsights.info/data/response',
        'getEndpoint' => 'https://api.webinsights.info/data/response/?uri={uri}',
        'updateEndpoint' => 'https://api.webinsights.info/collection/job/status/{runId}',
        'updateInterval' => 5
    ];

    private string $runId;

    private string $setEndPoint;
    private string $getEndPoint;
    private string $updateEndPoint;

    private int $updateInterval;

    private Client $client;
    private int $count = 0;

    public function __construct(array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $this->runId = $options['runId'];

        $this->setEndPoint = $options['setEndpoint'];
        $this->getEndPoint = $options['getEndpoint'];
        $this->updateEndPoint = $options['updateEndpoint'];

        $this->updateInterval = $options['updateInterval'];

        $this->client = new Client();
    }

    public function setHttpResponse(UriInterface $uri, HttpResponse $response): void
    {
        $data = [
            'runId' => $this->runId,
            'uri' => (string)$uri,
            'response' => $response->jsonSerialize()
        ];

        $this->client->post($this->setEndPoint, [
            RequestOptions::JSON => $data
        ]);
    }

    private function update(): void
    {
        $data = [
            'hit' => $this->responsesCached,
            'miss' => $this->responsesNotCached,
        ];

        $this->client->post(str_replace('{runId}', $this->runId, $this->updateEndPoint), [RequestOptions::JSON => $data]);

        $this->responsesCached = 0;
        $this->responsesNotCached = 0;
    }

    public function getHttpResponse(UriInterface $uri): HttpResponse|false
    {
        $url = str_replace('{uri}', urlencode((string)$uri), $this->getEndPoint);

        $this->count++;

        try {
            $data = $this->client->get($url);
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                $this->responsesNotCached++;
                return false;
            } else {
                throw $exception;
            }
        }

        $this->responsesCached++;
        $rawData = json_decode((string)$data->getBody(), true);

        if ($this->count % $this->updateInterval == 0) {
            $this->update();
        }

        return HttpResponse::fromArray($rawData['data']);
    }

    public function finish(): void
    {
        $this->update();
    }
}
