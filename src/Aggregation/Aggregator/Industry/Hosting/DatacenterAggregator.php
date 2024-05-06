<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Industry\Hosting;

use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Url\IPLocationClassifier;

class DatacenterAggregator extends CountingAggregator
{
    protected int $limit = 30;

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $as = $classificationResult->getTagsStartingWithString(IPLocationClassifier::TAG_HOSTING_LOCATION_PREFIX . 'as', true, true);

        if (count($as) === 1) {
            $this->increaseCount($as);
        }
    }
}
