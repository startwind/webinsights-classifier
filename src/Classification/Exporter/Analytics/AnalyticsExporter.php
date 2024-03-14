<?php

namespace Startwind\WebInsights\Classification\Exporter\Analytics;

use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Exporter\Exporter;

abstract class AnalyticsExporter implements Exporter
{
    protected array $tags = [];

    protected int $count = 0;

    public function export(ClassificationResult $classificationResult): void
    {
        $this->count++;

        foreach ($classificationResult->getTags() as $tag) {
            if (str_starts_with($tag, 'ip:')) continue;
            if (array_key_exists($tag, $this->tags)) {
                $this->tags[$tag]++;
            } else {
                $this->tags[$tag] = 1;
            }
        }
    }
}
