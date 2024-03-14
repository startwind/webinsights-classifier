<?php

namespace Startwind\WebInsights\Aggregation\Aggregator;

use Startwind\WebInsights\Classification\ClassificationResult;

abstract class TagCountingAggregator extends CountingAggregator
{
    protected string $tag = '';

    protected string $unknown = 'unknown';

    protected bool $addUnknownTag = false;
    protected bool $dissolveUnderscore = false;

    protected int $aggregationCount = 0;

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $this->aggregationCount++;

        $tags = $classificationResult->getTagsStartingWithString($this->tag, true, $this->dissolveUnderscore);

        if (empty($tags) && $this->addUnknownTag) {
            $tags[] = $this->unknown;
        }

        $this->increaseCount($tags);
    }
}
