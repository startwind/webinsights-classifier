<?php

namespace Startwind\WebInsights\Aggregation\Retriever;

use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Configuration\Initializable;

interface Retriever extends Initializable
{
    public function next(): ClassificationResult|false;
}
