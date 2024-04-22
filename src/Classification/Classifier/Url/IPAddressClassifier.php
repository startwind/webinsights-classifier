<?php

namespace Startwind\WebInsights\Classification\Classifier\Url;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\Enricher\IPEnricher;
use Startwind\WebInsights\Response\HttpResponse;

class IPAddressClassifier implements Classifier
{
    public const CLASSIFIER_PREFIX = 'ip:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->hasEnrichment(IPEnricher::getIdentifier())) {
            $data = $httpResponse->getEnrichment(IPEnricher::getIdentifier());
            return [self::CLASSIFIER_PREFIX . $data[IPEnricher::FIELD_IP]];
        } else {
            return [];
        }
    }
}
