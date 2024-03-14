<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class JimdoClassifier implements Classifier
{
    private const TAG = CmsClassifier::CLASSIFIER_PREFIX . 'jimdo';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->containsAny([
            'jimdo_web_css  ',
            'jimdoData',
            'isJimdoHelpCenter',
            'jimdo_layout_css',
            'j-m-jimdo-styles',
            'a.jimdo.com'
        ])) {
            return [self::TAG];
        }

        return [];
    }
}
