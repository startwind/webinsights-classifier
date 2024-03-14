<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\System\Cms;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;
use Startwind\WebInsights\Classification\Classifier\Cms\CmsClassifier;

class CmsOverviewAggregator extends TagCountingAggregator
{
    protected string $name = "CMS Overview";

    protected string $tag = CmsClassifier::CLASSIFIER_PREFIX;

    public function finish(): AggregationResult
    {
        $translate = [
            'wordpress' => 'WordPress'
        ];

        $cmsCount = $this->getCount();

        arsort($cmsCount);

        $firstName = array_key_first($cmsCount);
        $first = array_shift($cmsCount);

        if (array_key_exists($firstName, $translate)) {
            $firstName = $translate[$firstName];
        }

        if ($this->aggregationCount > 0) {
            $percent = (int)($first / $this->aggregationCount * 100);
        } else {
            $percent = 0;
        }

        $results['first'] = [
            'name' => $firstName,
            'count' => $first,
            'percent' => $percent
        ];

        return new AggregationResult($results, '', '', static::class);
    }
}
