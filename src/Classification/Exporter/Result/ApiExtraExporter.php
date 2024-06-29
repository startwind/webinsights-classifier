<?php

namespace Startwind\WebInsights\Classification\Exporter\Result;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Exporter\Exporter;
use Startwind\WebInsights\Classification\Exporter\OutputAwareExporter;
use Symfony\Component\Console\Output\OutputInterface;

class ApiExtraExporter implements Exporter, OutputAwareExporter
{
    private array $defaultOptions = [
        'endpoint' => 'https://api.webinsights.info/classifier/extras'
    ];

    private OutputInterface $output;

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    private string $exportEndpoint;

    private Client $client;

    private int $processedWebsites = 0;

    private array $data = [];

    const BLOCK_SIZE = 1000;

    private int $count = 0;
    private int $found = 0;

    public function __construct($notNeeded, $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $this->client = new Client();

        $this->exportEndpoint = $options['endpoint'];
    }

    public function export(ClassificationResult $classificationResult): void
    {
        $this->count++;
        $this->processedWebsites++;

        $found = false;

        foreach ($classificationResult->getTags() as $tag) {
            if (str_starts_with($tag, Classifier::TAG_PREFIX_EXTRA)) {
                $found = true;
            }
        }

        if (!$found) return;

        $this->found++;

        $this->data[] = [
            'tags' => $classificationResult->getTags(),
            'uri' => (string)$classificationResult->getUri()
        ];

        if (count($this->data) > self::BLOCK_SIZE) {
            $this->finish();
            $this->data = [];
        }
    }

    public function finish(): string
    {
        $this->output->writeln('<info>Processed ' . $this->count . ' urls, found ' . $this->found . ' with extras.</info>');

        $this->client->post($this->exportEndpoint, [
                RequestOptions::JSON => ['data' => $this->data]
            ]
        );

        return '';
    }
}
