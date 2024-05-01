<?php

namespace Startwind\WebInsights\Classification\Classifier\Hosting;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class HostingCompanyClassifier implements Classifier
{
    private const CLASSIFIER_PREFIX = 'industry:hosting';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $count = 0;

        foreach ($existingTags as $existingTag) {
            if (str_starts_with($existingTag, HostingProductsClassifier::TAG_PREFIX,)) {
                $count++;
            }
        }

        if ($count > 4) {
            return [self::CLASSIFIER_PREFIX];
        } else {
            return [];
        }
    }
}
