<?php

namespace Startwind\WebInsights\Classification\Exporter\Result;

use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Exporter\Exporter;
use Startwind\WebInsights\Classification\Exporter\OutputAwareExporter;
use Symfony\Component\Console\Output\OutputInterface;

class SimpleCliExporter implements Exporter, OutputAwareExporter
{
    private OutputInterface $output;

    private int $count = 0;

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function export(ClassificationResult $classificationResult): void
    {
        $this->count++;
        $count = str_pad((string)$this->count, 4, ' ');
        $this->output->writeln(' ' . $count . ' <info>' . $classificationResult->getUri() . '</info> ' . implode(', ', $classificationResult->getTags()));
    }

    public function comment(string $string): void
    {
        $this->output->writeln(['', '<comment># ' . $string . '</comment>']);
    }

    public function finish(): string
    {
        return '';
    }
}
