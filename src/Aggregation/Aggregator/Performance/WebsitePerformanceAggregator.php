<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Performance;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Characteristic\Http\TransferTimeClassifier;

class WebsitePerformanceAggregator extends CountingAggregator
{
    protected string $description = "This metric represents the speed of the web application. It measures the time the server needs to answer the request for the homepage. However websites could still be slow as the main effort arises in the browser.";
    protected string $name = "Website Performance";

    protected int $limit = -1;

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $tags = $classificationResult->getTagsStartingWithString(TransferTimeClassifier::TAG, true);

        if (count($tags) != 0) {
            $tag = array_pop($tags);
            $this->increaseCount($tag);
        }
    }

    public function finish(): AggregationResult
    {
        $result = parent::finish();

        $values = $result->getResults();

        ksort($values);

        $newValues = [];

        foreach ($values as $key => $value) {
            $newValues['< ' . $key . ' ms'] = $value;
        }

        $result->setResults($newValues);

        return $result;
    }
}
