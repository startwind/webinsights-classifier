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
        'finishEndpoint' => 'https://api.webinsights.info/collection/job/finish/{runId}',
        'last' => false
    ];

    private string $exportEndpoint;

    private Client $client;

    private string $runId;

    private bool $isLastRun;
    private string $finishEndpoint;

    public function __construct($notNeeded, $options = [])
    {
        $options = array_merge($this->defaultOptions, $options['options']);
        $this->client = new Client();

        $this->runId = $options['runId'];
        $this->exportEndpoint = $options['endpoint'];
        $this->finishEndpoint = $options['finishEndpoint'];
        $this->isLastRun = $options['last'];
    }

    public function export(ClassificationResult $classificationResult): void
    {
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
            $this->client->get(str_replace('{runId}', $this->runId, $this->finishEndpoint));
        }
        return '';
    }
}
