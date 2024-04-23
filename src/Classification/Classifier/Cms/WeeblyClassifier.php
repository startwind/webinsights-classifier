<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class WeeblyClassifier implements Classifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'weebly';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
                'W.configDomain = "www.weebly.com"',
                'weebly-icon',
                'weebly-footer-signup-container',
                'Weebly.footer.setupContainer'
            ]
        )) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        }

        return [];
    }
}
