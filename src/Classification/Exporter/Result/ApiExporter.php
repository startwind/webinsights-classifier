<?php

namespace Startwind\WebInsights\Classification\Exporter\Result;

use GuzzleHttp\Client;
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

    private Client $client;

    private string $runId;

    private bool $isLastRun;

    private string $updateEndpoint;
    private string $finishEndpoint;

    private int $processedWebsites = 0;

    public function __construct($notNeeded, $options = [])
    {
        $options = array_merge($this->defaultOptions, $options['options']);
        $this->client = new Client();

        $this->runId = $options['runId'];

        $this->exportEndpoint = $options['endpoint'];
        $this->finishEndpoint = $options['finishEndpoint'];
        $this->updateEndpoint = $options['updateEndpoint'];

        $this->isLastRun = $options['last'];
    }

    public function export(ClassificationResult $classificationResult): void
    {
        $this->processedWebsites++;

        $data = [
            'runId' => $this->runId,
            'tags' => $classificationResult->getTags(),
            'uri' => (string)$classificationResult->getUri()
        ];

        $this->client->post($this->exportEndpoint, [
                RequestOptions::JSON => $data
            ]
        );
    }

    public function finish(): string
    {
        if ($this->isLastRun) {
            $url = str_replace('{runId}', $this->runId, $this->finishEndpoint);
            $url = str_replace('{miss}', $this->processedWebsites, $url);
            $this->client->get($url);
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
