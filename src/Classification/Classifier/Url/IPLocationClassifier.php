<?php

namespace Startwind\WebInsights\Classification\Classifier\Url;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\Cdn\CDNClassifier;
use Startwind\WebInsights\Response\Enricher\GeoLocationEnricher;
use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Util\TagHelper;

class IPLocationClassifier implements Classifier
{
    public const TAG_HOSTING_LOCATION_PREFIX = 'hosting:location:';
    public const TAG_HOSTING_LOCATION_ISP_PREFIX = self::TAG_HOSTING_LOCATION_PREFIX . 'isp:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $tags = [];

        if ($httpResponse->hasEnrichment(GeoLocationEnricher::getIdentifier())) {
            $data = $httpResponse->getEnrichment(GeoLocationEnricher::getIdentifier());

            if (array_key_exists('countryCode', $data) && $data['countryCode']) {
                $tags[] = self::TAG_HOSTING_LOCATION_PREFIX . 'country:' . TagHelper::normalize($data['countryCode']);
            }

            if (array_key_exists('isp', $data) && $data['isp']) {
                $tags[] = self::TAG_HOSTING_LOCATION_ISP_PREFIX . TagHelper::normalize($data['isp']);

                if (TagHelper::normalize($data['isp']) === "fastly_inc") {
                    $tags[] = CDNClassifier::TAG_PREFIX . 'fastly';
                }
            }

            if (array_key_exists('as', $data) && $data['as']) {
                $asParts = explode(' ', $data['as']);
                $as = str_replace('as', '', strtolower($asParts[0]));
                $tags[] = self::TAG_HOSTING_LOCATION_PREFIX . 'as:' . TagHelper::normalize($data['as']);
                $tags[] = self::TAG_HOSTING_LOCATION_PREFIX . 'asn:' . $as;
            }
        }

        return $tags;
    }
}
