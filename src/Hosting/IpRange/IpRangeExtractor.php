<?php

namespace Startwind\WebInsights\Hosting\IpRange;

interface IpRangeExtractor
{
    public function getIpRange(string $as): array;
}
