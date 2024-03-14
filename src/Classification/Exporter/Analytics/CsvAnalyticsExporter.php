<?php

namespace Startwind\WebInsights\Classification\Exporter\Analytics;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;
use Startwind\WebInsights\Classification\Exporter\LoggerAwareExporter;
use Startwind\WebInsights\Configuration\Exception\MissingOptionException;
use Startwind\WebInsights\Util\FilenameHelper;

class CsvAnalyticsExporter extends AnalyticsExporter implements LoggerAwareExporter
{
    use LoggerAwareTrait;

    private string $filename;

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

        if (file_exists($this->filename)) {
            $fp = fopen($this->filename, 'r');
            fgetcsv($fp);

            $first = true;

            while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {
                if ($first && $data[1] > 0 && $data[2] > 0) {
                    $this->count = (int)($data[1] / $data[2]) * 100;
                    $first = false;
                }
                $this->tags[$data[0]] = $data[1];
            }
        }
    }

    public function finish(): string
    {
        $fp = fopen($this->filename, 'w');
        fputcsv($fp, ['Tag', 'Absolute', 'Percent']);

        foreach ($this->tags as $tag => $count) {
            if (is_int($count)) {
                $percent = round($count / $this->count * 100);
                fputcsv($fp, [$tag, $count, $percent]);
            } else {
                $this->logger->log(LogLevel::ALERT, 'CsvAnalyticsExporter: $count has an invalid value: ' . $count . '. Skipping export for tag ' . $tag);
            }
        }

        fclose($fp);

        return '<info>Analytics file successfully exported to ' . $this->filename . '</info>';
    }
}
