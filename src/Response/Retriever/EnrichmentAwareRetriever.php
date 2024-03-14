<?php

namespace Startwind\WebInsights\Response\Retriever;

use Startwind\WebInsights\Response\Enricher\Enricher;

interface EnrichmentAwareRetriever
{
    public function addEnricher(Enricher $enricher): void;
}
