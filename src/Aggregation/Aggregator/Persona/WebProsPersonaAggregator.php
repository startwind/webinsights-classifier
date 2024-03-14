<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Persona;

use Startwind\WebInsights\Aggregation\AggregationResult;
use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\UrlAwareAggregationTrait;
use Startwind\WebInsights\Aggregation\UrlAwareAggregationResult;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Ecommerce\EcommerceClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Keyword\KeywordClassifier as HtmlBodyKeywordClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Language\LanguageClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Meta\KeywordClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Plugin\PluginClassifier;

class WebProsPersonaAggregator extends CountingAggregator
{
    use UrlAwareAggregationTrait;

    const PERSONA_HOSTER = 'hoster';
    const PERSONA_PROFESSIONAL = 'professionals';
    const PERSONA_AGENCY = 'agency';
    const PERSONA_E_COMMERCE = 'ecommerce';

    public function aggregate(ClassificationResult $classificationResult): void
    {
        if ($classificationResult->hasTag(EcommerceClassifier::TAG_ECOMMERCE)) {
            $this->increaseCount(self::PERSONA_E_COMMERCE);
            $this->addUrl((string)$classificationResult->getUri(), self::PERSONA_E_COMMERCE);
        }

        if ($classificationResult->hasTag(KeywordClassifier::PREFIX . 'agency')
            || count($classificationResult->getTagsStartingWithString(KeywordClassifier::PREFIX . 'agency')) > 0
            || count($classificationResult->getTagsStartingWithString(HtmlBodyKeywordClassifier::PREFIX . 'agency')) > 0
            || count($classificationResult->getTagsStartingWithString(HtmlBodyKeywordClassifier::PREFIX . 'web_development')) > 0
            || count($classificationResult->getTagsStartingWithString(HtmlBodyKeywordClassifier::PREFIX . 'web_design'))) {
            $this->increaseCount(self::PERSONA_AGENCY);
            $this->addUrl((string)$classificationResult->getUri(), self::PERSONA_AGENCY);
        }

        if (count($classificationResult->getTagsStartingWithString(PluginClassifier::PREFIX_MONITORING)) > 0
            || count($classificationResult->getTagsStartingWithString(LanguageClassifier::TAG_PREFIX)) >= 2) {
            $this->increaseCount(self::PERSONA_PROFESSIONAL);
            $this->addUrl((string)$classificationResult->getUri(), self::PERSONA_PROFESSIONAL);
        }
    }

    public function finish(): AggregationResult
    {
        return UrlAwareAggregationResult::fromAggregationResult(parent::finish(), $this->getUrls());
    }
}
