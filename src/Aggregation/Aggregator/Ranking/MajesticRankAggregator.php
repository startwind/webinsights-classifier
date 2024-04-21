<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Ranking;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\UrlAwareAggregationTrait;
use Startwind\WebInsights\Classification\ClassificationResult;

class MajesticRankAggregator extends CountingAggregator
{
    use UrlAwareAggregationTrait;

    private array $top20 = [];

    private int $minRank = 10000000000;

    private const TAG_PREFIX = 'majestic_rank:';

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $tags = $classificationResult->getTagsStartingWithString(self::TAG_PREFIX);

        if (count($tags) > 0) {
            $rank = (int)str_replace(self::TAG_PREFIX, '', $tags[0]);

            if ($rank < $this->minRank) {
                $this->minRank = $rank;
                array_pop($this->top20);
                $this->top20[] = (string)$classificationResult->getUri();
                sort($this->top20);
            }
        }
    }

    public function finish(): AggregationResult
    {
        return new AggregationResult(
            ['top20' => $this->top20],
            'The top 20 websites according to majestic rank',
            'majestic_rank',
            self::class
        );
    }
}
