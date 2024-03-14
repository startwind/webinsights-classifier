<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http\Cdn;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\Enricher\GeoLocationEnricher;
use Startwind\WebInsights\Response\HttpResponse;

class CloudflareClassifier extends HttpClassifier
{
    const TAG = CDNClassifier::TAG_PREFIX . 'cloudflare';

    protected function doHttpClassification(HttpResponse $response): array
    {
        if ($response->hasEnrichment(GeoLocationEnricher::getIdentifier())) {
            $data = $response->getEnrichment(GeoLocationEnricher::getIdentifier());
            if (array_key_exists('isp', $data) && str_contains(strtolower($data['isp']), 'cloudflare')) {
                return [self::TAG];
            }
        }

        if ($response->headerContains('server', 'cloudflare')) {
            return [self::TAG];
        } else {
            return [];
        }
    }
}
