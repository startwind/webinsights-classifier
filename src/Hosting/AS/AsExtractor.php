<?php

namespace Startwind\WebInsights\Hosting\AS;

interface AsExtractor
{
    public function getAs(string $domain): string;
}
