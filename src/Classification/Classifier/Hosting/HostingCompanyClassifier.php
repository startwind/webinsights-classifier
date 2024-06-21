<?php

namespace Startwind\WebInsights\Classification\Classifier\Hosting;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class HostingCompanyClassifier implements Classifier
{
    public const CLASSIFIER_PREFIX = 'industry:hosting';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'Wix.com Website Builder',
            'dan.com',
            'Log in to Plesk to create websites and set up hosting',
            'It is specially designed to help web professionals manage web',
            'Website coming soon! Stay tuned.',
            'Parallels is a worldwide leader in virtualization and automation'
        ])) {
            return [];
        }

        $count = 0;

        if (in_array('http:status-code:403', $existingTags)) return [];

        $minProducts = 4;

        // @todo title does not contain hosting magazine

        if ($httpResponse->getHtmlDocument()->contains('hosting')) $minProducts = 3;

        foreach ($existingTags as $existingTag) {
            if (str_starts_with($existingTag, HostingProductsClassifier::TAG_PREFIX)
                && !str_starts_with($existingTag, HostingProductsClassifier::TAG_PREFIX . 'cms')
                && !str_starts_with($existingTag, HostingProductsClassifier::TAG_PREFIX . 'ecommerce')
                && count(explode(':', $existingTag)) === 4) {
                $count++;
            }
        }

        if ($count > $minProducts) {
            return [self::CLASSIFIER_PREFIX];
        } else {
            return [];
        }
    }
}
