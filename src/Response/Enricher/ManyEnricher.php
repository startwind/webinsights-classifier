<?php

namespace Startwind\WebInsights\Response\Enricher;

interface ManyEnricher
{
    /**
     * @param \Startwind\WebInsights\Response\HttpResponse[] $responses
     */
    public function enrichMany(array $responses): void;
}
