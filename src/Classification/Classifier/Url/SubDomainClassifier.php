<?php

namespace Startwind\WebInsights\Classification\Classifier\Url;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\UrlClassifier;

class SubDomainClassifier extends UrlClassifier
{
    public const CLASSIFIER_PREFIX = 'domain' . Classifier::TAG_SEPARATOR;

    protected function doClassification(UriInterface $uri, array $existingTags): array
    {
        $host = $uri->getHost();

        $parts = explode('.', $host);

        if (count($parts) === 2) {
            return [self::CLASSIFIER_PREFIX . 'main'];
        }

        if (count($parts) > 3) {
            return [self::CLASSIFIER_PREFIX . 'sub'];
        }

        if ($parts[0] === 'www') {
            return [self::CLASSIFIER_PREFIX . 'main'];
        }

        if (in_array($parts[1], ['com', 'co', 'net', 'edu', 'gov', 'org'])) {
            return [self::CLASSIFIER_PREFIX . 'main'];
        }

        return [self::CLASSIFIER_PREFIX . 'sub'];
    }
}
