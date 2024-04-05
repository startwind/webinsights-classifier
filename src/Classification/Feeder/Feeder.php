<?php

namespace Startwind\WebInsights\Classification\Feeder;

use Startwind\WebInsights\Classification\Domain\DomainContainer;

interface Feeder
{
    public function getDomainContainer(): DomainContainer;
}
