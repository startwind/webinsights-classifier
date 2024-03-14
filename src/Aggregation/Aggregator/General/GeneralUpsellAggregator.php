<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\General;

use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Characteristic\Http\TransferTimeClassifier;
use Startwind\WebInsights\Classification\Classifier\Cms\WordPress\WordPressPluginClassifier;
use Startwind\WebInsights\Classification\Classifier\Service\Email\EmailServiceClassifier;

class GeneralUpsellAggregator extends CountingAggregator
{
    private const PERFORMANCE_SLOW = 5000;

    public const EMAIL_NONE = 'email_none';
    public const SEO_SOLUTION = 'seo_solution';
    public const SLOW_WEBSITES = 'websites_slow';

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $emailTags = $classificationResult->getTagsStartingWithString(EmailServiceClassifier::TAG_PREFIX, true, true);
        if (empty($emailTags)) $this->increaseCount(self::EMAIL_NONE);

        if ($classificationResult->hasTag(WordPressPluginClassifier::TAG_PREFIX . 'yoast')) $this->increaseCount(self::SEO_SOLUTION);

        $tags = $classificationResult->getTagsStartingWithString(TransferTimeClassifier::TAG, true);

        if (count($tags) != 0) {
            $tag = array_pop($tags);
            if ((int)$tag > self::PERFORMANCE_SLOW) {
                $this->increaseCount(self::SLOW_WEBSITES);
            }
        }
    }
}
