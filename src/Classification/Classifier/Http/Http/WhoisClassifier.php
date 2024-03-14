<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\Enricher\WhoisEnricher;
use Startwind\WebInsights\Response\HttpResponse;

class WhoisClassifier implements Classifier
{
    public const TAG_PREFIX_ISSUER = 'tech:whois:registrar:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->hasEnrichment(WhoisEnricher::getIdentifier())) {
            $whoisInfo = $httpResponse->getEnrichment(WhoisEnricher::getIdentifier());
            if (array_key_exists(WhoisEnricher::FIELD_REGISTRAR, $whoisInfo)) {
                return [self::TAG_PREFIX_ISSUER . $whoisInfo[WhoisEnricher::FIELD_REGISTRAR]];
            }
        }
        return [];
    }
}
