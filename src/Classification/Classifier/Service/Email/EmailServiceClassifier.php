<?php

namespace Startwind\WebInsights\Classification\Classifier\Service\Email;

use GuzzleHttp\Psr7\Uri;
use Psr\Log\LoggerInterface;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\LoggerAwareClassifier;
use Startwind\WebInsights\Response\Enricher\MXEnricher;
use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Util\UrlHelper;

class EmailServiceClassifier implements Classifier, LoggerAwareClassifier
{
    private LoggerInterface $logger;

    public const TAG_PREFIX = 'service:email:';

    private array $knownEntries = [
        'google' => 'google',
        'united-domains:de' => 'udag.de',
        'outlook' => 'outlook',
        'cloudflare' => 'mx.cloudflare.net',
        'ovh' => 'mail.ovh.net',
        'cleanmx' => 'cleanmx.pt',
        'yahoo' => 'yahoodns.net',
        'titan' => 'titan.email',
        'zoho' => 'zoho.com',
        'hostinger' => 'hostinger.in'
    ];

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->hasEnrichment(MXEnricher::getIdentifier())) {
            $records = $httpResponse->getEnrichment(MXEnricher::getIdentifier());
            foreach ($records[MXEnricher::FIELD_RECORDS] as $record) {
                foreach ($this->knownEntries as $key => $entry) {
                    if (str_contains(strtolower($record), $entry)) {
                        return [self::TAG_PREFIX . $key];
                    }
                }

                $domain = UrlHelper::getDomain($httpResponse->getRequestUri());

                if (!str_contains($record, $domain)) {
                    $mxDomain = UrlHelper::getDomain(new Uri('https://' . $record));
                    return [self::TAG_PREFIX . str_replace('.', '_', $mxDomain)];
                } else {
                    return [self::TAG_PREFIX . 'self-hosted'];
                }
            }
        }

        return [];
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
