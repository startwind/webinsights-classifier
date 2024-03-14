<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\Enricher\SSLEnricher;
use Startwind\WebInsights\Response\HttpResponse;

class SSLCertificateClassifier implements Classifier
{
    public const TAG_PREFIX_ISSUER = 'tech:ssl:issuer:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->hasEnrichment(SSLEnricher::getIdentifier())) {
            $sslInfo = $httpResponse->getEnrichment(SSLEnricher::getIdentifier());
            if (array_key_exists('issuer', $sslInfo)) {
                return [self::TAG_PREFIX_ISSUER . $sslInfo['issuer']['O']];
            }
        }
        return [];
    }
}
