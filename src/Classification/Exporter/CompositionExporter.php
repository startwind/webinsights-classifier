<?php

namespace Startwind\WebInsights\Classification\Exporter;

use Psr\Log\LoggerInterface;
use Startwind\WebInsights\Classification\ClassificationResult;

class CompositionExporter implements Exporter, LoggerAwareExporter
{
    /** @var Exporter[] */
    private array $exporter = [];

    public function addExporter(Exporter $exporter)
    {
        $reflect = new \ReflectionClass($exporter);
        $this->exporter[$reflect->getShortName()] = $exporter;
    }

    public function export(ClassificationResult $classificationResult): void
    {
        foreach ($this->exporter as $exporter) {
            $exporter->export($classificationResult);
        }
    }

    public function comment(string $string): void
    {
        foreach ($this->exporter as $exporter) {
            $exporter->comment($string);
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        foreach ($this->exporter as $exporter) {
            if ($exporter instanceof LoggerAwareExporter) {
                $exporter->setLogger($logger);
            }
        }
    }

    public function finish(): string
    {
        $result = "";

        foreach ($this->exporter as $name => $exporter) {
            $exporterResult = $exporter->finish();
            if ($exporterResult) {
                $result .= 'Exporter ' . $name . ': ' . $exporterResult . "\n";
            }
        }

        return $result;
    }
}
