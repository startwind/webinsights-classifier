<?php

namespace Startwind\WebInsights\Classification\Exporter\Result;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Exporter\Exporter;

class ApiExporter implements Exporter
{
    private array $defaultOptions = [
        'endpoint' => 'https://api.webinsights.info/classifier/data',
        'finishEndpoint' => 'https://api.webinsights.info/collection/job/finish/{runId}?miss={miss}',
        'updateEndpoint' => 'https://api.webinsights.info/collection/job/status/{runId}',
        'last' => false
    ];

    private string $exportEndpoint;

    private int $bulkMaxSize = 100;

    private Client $client;

    private string $runId;

    private bool $isLastRun;

    private string $updateEndpoint;
    private string $finishEndpoint;

    private int $processedWebsites = 0;

    private array $data = [];

    public function __construct($notNeeded, $options = [])
    {
        if (array_key_exists('options', $options)) {
            $options = array_merge($this->defaultOptions, $options['options']);
        } else {
            $options = $this->defaultOptions;
        }
        $this->client = new Client();

        if (array_key_exists('runId', $options)) {
            $this->runId = $options['runId'];
        } else {
            $this->runId = 'manual_' . time();
        }

        $this->exportEndpoint = $options['endpoint'];
        $this->finishEndpoint = $options['finishEndpoint'];
        $this->updateEndpoint = $options['updateEndpoint'];

        $this->isLastRun = $options['last'];
    }

    public function export(ClassificationResult $classificationResult): void
    {
        $this->processedWebsites++;

        $this->data[] = [
            'runId' => $this->runId,
            'tags' => $classificationResult->getTags(),
            'uri' => (string)$classificationResult->getUri()
        ];

        if (count($this->data) > $this->bulkMaxSize) {
            $this->flushData();
        }
    }

    private function flushData(): void
    {
        var_dump(json_encode(['data' => $this->data]));

        $this->client->post('https://api.webinsights.info/classifier/datas', [
                RequestOptions::JSON => ['data' => $this->data]
            ]
        );

        $this->data = [];
    }

    public function finish(): string
    {
        $this->flushData();

        if ($this->isLastRun) {
            $url = str_replace('{runId}', $this->runId, $this->finishEndpoint);
            $url = str_replace('{miss}', $this->processedWebsites, $url);
            try {
                $this->client->get($url);
            } catch (ServerException $e) {
                // var_dump((string)$e->getResponse()->getBody());
            }
        }

        $this->update();

        return '';
    }

    private function update(): void
    {
        $data = [
            'hit' => 0,
            'miss' => $this->processedWebsites,
        ];

        $this->client->post(str_replace('{runId}', $this->runId, $this->updateEndpoint), [RequestOptions::JSON => $data]);
    }
}
