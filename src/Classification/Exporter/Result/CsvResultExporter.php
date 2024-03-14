<?php

namespace Startwind\WebInsights\Classification\Exporter\Result;

use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Exporter\Exporter;
use Startwind\WebInsights\Configuration\Exception\MissingOptionException;
use Startwind\WebInsights\Util\FilenameHelper;

class CsvResultExporter implements Exporter
{
    private string $filename;

    private $handle;

    public function __construct(string $runId, array $options)
    {
        if (!array_key_exists('filename', $options)) {
            throw new MissingOptionException('Mandatory option "filename" missing.');
        }

        $this->filename = FilenameHelper::process($options['filename'], ['runId' => $runId]);

        $dir = dirname($this->filename);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = FilenameHelper::process($this->filename);

        if (file_exists($filename)) {
            $this->handle = fopen($filename, 'a');
        } else {
            $this->handle = fopen($filename, 'w');
            fputcsv($this->handle, ['Classified object', 'Crawl Date', 'Tag Count', 'Tags']);
        }
    }

    public function export(ClassificationResult $classificationResult): void
    {
        fputcsv($this->handle, [
            (string)$classificationResult->getUri(),
            date('Y-m-d H:i:s'),
            count($classificationResult->getTags()),
            implode(', ', $classificationResult->getTags())
        ]);
    }

    public function finish(): string
    {
        fclose($this->handle);
        return '<info>Result successfully exported to ' . $this->filename . '</info>';
    }
}
