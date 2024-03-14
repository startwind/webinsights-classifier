<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http\Cdn;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\Enricher\GeoLocationEnricher;
use Startwind\WebInsights\Response\HttpResponse;

class AkamaiClassifier extends HttpClassifier
{
    const TAG_PREFIX = CDNClassifier::TAG_PREFIX . 'akamai';

    protected function doHttpClassification(HttpResponse $response): array
    {
        if ($response->hasEnrichment(GeoLocationEnricher::getIdentifier())) {
            $data = $response->getEnrichment(GeoLocationEnricher::getIdentifier());
            if (array_key_exists('isp', $data) && str_contains(strtolower($data['isp']), 'akamai')) {
                return [self::TAG_PREFIX];
            }
        }

        if ($response->hasHeader('Akamai-Grn')) {
            return [self::TAG_PREFIX];
        } else {
            return [];
        }
    }
}
