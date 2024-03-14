<?php

namespace Startwind\WebInsights\Aggregation\Aggregator;

abstract class SimpleAggregator implements Aggregator
{
    protected string $name = "";
    protected string $description = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.";

    protected function getDescription(): string
    {
        return $this->description;
    }

    protected function getName(): string
    {
        return $this->name;
    }
}
