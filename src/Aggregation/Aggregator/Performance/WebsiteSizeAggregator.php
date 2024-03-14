<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Performance;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Characteristic\Html\SizeClassifier;

class WebsiteSizeAggregator extends CountingAggregator
{
    protected string $description = "The website size includes the plain size of the HTML that is created for rendering the homepage. It does not include CSS, JS, images or other external files.";
    protected string $name = "Website Size";

    private const LIMIT = 5;

    protected int $limit = -1;

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $tags = $classificationResult->getTagsStartingWithString(SizeClassifier::TAG, true);

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
            if ($key > self::LIMIT) {
                $limitKey = '> ' . self::LIMIT . ' MB';
                if (!array_key_exists($limitKey, $newValues)) {
                    $newValues['> ' . self::LIMIT . ' MB'] = 0;
                }
                $newValues['> ' . self::LIMIT . ' MB'] = $newValues['> ' . self::LIMIT . ' MB'] + $value;
            } else {
                $newValues['< ' . $key . ' MB'] = $value;
            }
        }

        $result->setResults($newValues);

        return $result;
    }
}
