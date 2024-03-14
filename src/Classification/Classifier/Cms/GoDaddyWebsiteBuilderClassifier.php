<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class GoDaddyWebsiteBuilderClassifier implements Classifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'godaddy-website-builder';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny(['https://www.godaddy.com/websites/website-builder'])) {
            return [self::TAG];
        }

        if ($httpResponse->headerContains('Content-Security-Policy', "'frame-ancestors 'self' godaddy.com *.godaddy.com")) {
            return [self::TAG];
        }

        return [];
    }
}
