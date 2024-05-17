<?php

namespace Startwind\WebInsights\Classification\Classifier;

use Startwind\WebInsights\Response\HttpResponse;

interface Classifier
{
    public const TAG_SEPARATOR = ':';

    public const TAG_PREFIX_EXTRA = 'extra_';

    /**
     * @throws \Startwind\WebInsights\Classification\Exception\Exception
     */
    public function classify(HttpResponse $httpResponse, array $existingTags): array;
}
