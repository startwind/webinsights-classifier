<?php

namespace Startwind\WebInsights\Util;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\UrlAwareAggregationResult;

abstract class StorageHelper
{
    static public function store(UrlAwareAggregationResult $aggregationResult, string $outputDir): array
    {
        $urlGroup = $aggregationResult->getUrls();

        $files = [];

        foreach ($urlGroup as $group => $urls) {
            $filename = self::getFilename($aggregationResult, $group, $outputDir) . '.csv';
            $files[$group] = $filename;

            $handle = fopen($filename, 'w');
            $fields = ['Url'];

            foreach ($urls[0][UrlAwareAggregationResult::DATA_FIELD] as $key => $value) {
                $fields[] = $key;
            }
            fputcsv($handle, $fields);

            foreach ($urls as $url) {
                fputcsv($handle, array_merge(['url' => $url[UrlAwareAggregationResult::URL_FIELD]], $url[UrlAwareAggregationResult::DATA_FIELD]));
            }

            fclose($handle);
        }

        return $files;
    }

    private static function getFilename(AggregationResult $aggregationResult, $key, string $outputDir, $withDir = true): string
    {
        $fileName = md5($key . $aggregationResult->getGenerator());

        if ($withDir) {
            return $outputDir . $fileName;
        } else {
            return $fileName;
        }
    }
}
