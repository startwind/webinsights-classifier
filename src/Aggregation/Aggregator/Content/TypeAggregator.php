<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Content;

use Startwind\WebInsights\Aggregation\Aggregator\TagCountingAggregator;

class TypeAggregator extends TagCountingAggregator
{
    protected string $name = "Website Type Distribution";

    protected string $tag = "type:";

    protected bool $addUnknownTag = true;
}
