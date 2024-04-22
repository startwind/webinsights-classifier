<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Hosting;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\Aggregator;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Url\IPAddressClassifier;

class MultiIPDomainsAggregator implements Aggregator
{
    const COUNT_MAXIMUM = 10000;

    private array $ips = [];

    private $count = 0;

    public function aggregate(ClassificationResult $classificationResult): void
    {
        // This is mandatory because otherwise we could run out of memory.
        if (self::COUNT_MAXIMUM < $this->count) return;

        $ipTags = $classificationResult->getTagsStartingWithString(IPAddressClassifier::CLASSIFIER_PREFIX, true);

        if (count($ipTags) > 0) {
            $this->count++;
            $ip = ip2long(str_replace('_','.', $ipTags[0]));

            if (array_key_exists($ip, $this->ips)) {
                $this->ips[$ip]++;
            } else {
                $this->ips[$ip] = 1;
            }
        }
    }

    public function finish(): AggregationResult
    {
        $sum = 0;

        foreach ($this->ips as $count) {
            $sum += $count;
        }

        asort($this->ips);

        $results = [
            'average' => (int)$sum / $this->count,
            'ips_max' => array_slice(array_keys($this->ips), count($this->ips) - 10, 10)
        ];

        var_dump($results);

        return new AggregationResult($results, 'Get information about ips addresses.', 'IPInformation', self::class);
    }
}
