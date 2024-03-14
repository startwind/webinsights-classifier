<?php

namespace Startwind\WebInsights\Response\Enricher;

use Startwind\WebInsights\Response\HttpResponse;

interface Enricher
{
    public function enrich(HttpResponse $response): array|false;

    static public function getIdentifier(): string;
}
