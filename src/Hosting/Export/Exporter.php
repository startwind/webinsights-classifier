<?php

namespace Startwind\WebInsights\Hosting\Export;

interface Exporter
{
    public function export(string $as, array $ipRanges): void;
}
