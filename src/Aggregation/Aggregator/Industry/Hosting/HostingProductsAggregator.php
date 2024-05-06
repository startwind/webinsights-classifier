<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Industry\Hosting;

use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Hosting\HostingProductsClassifier;

class HostingProductsAggregator extends CountingAggregator
{
    public function aggregate(ClassificationResult $classificationResult): void
    {
        $products = $classificationResult->getTagsStartingWithString(HostingProductsClassifier::TAG_PREFIX, true, true);

        foreach ($products as $product) {
            $this->increaseCount($product);
        }
    }
}
