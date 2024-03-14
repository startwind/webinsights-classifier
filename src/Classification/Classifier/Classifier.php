<?php

namespace Startwind\WebInsights\Classification\Classifier;

use Startwind\WebInsights\Response\HttpResponse;

interface Classifier
{
    public const TAG_SEPARATOR = ':';

    /**
     * @throws \Startwind\WebInsights\Classification\Exception\Exception
     */
    public function classify(HttpResponse $httpResponse, array $existingTags): array;
}
