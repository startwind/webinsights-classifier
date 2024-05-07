<?php

namespace Startwind\WebInsights\Classification\Classifier\Service\Evaluation;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class TrustpilotClassifier implements Classifier
{
    public const TAG = 'service:evaluation:trustpilot';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'trustpilot.com',
            '"type":"trustpilot"',
        ])) {
            return [self::TAG];
        } else {
            return [];
        }
    }
}
