<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Persona;

use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Aggregation\Aggregator\UrlAwareAggregationTrait;
use Startwind\WebInsights\Classification\ClassificationResult;

class GeneralPersonaAggregator extends CountingAggregator
{
    use UrlAwareAggregationTrait;

    const RULE_TAGS_STARTING_WITH_COUNT_GREATER_THAN = 'tagsStartingWithCountGreaterThan';
    const RULE_HAS_TAGS = 'hasTags';

    const PERSONA_PREFIX = 'persona:';

    private array $personas;

    public function __construct(array $options = [])
    {
        $this->personas = $options['personas'];
    }

    public function aggregate(ClassificationResult $classificationResult): void
    {
        foreach ($this->personas as $persona) {
            $personaIdentifier = self::PERSONA_PREFIX . $persona['name'];
            $found = false;

            foreach ($persona['rules'] as $rule) {
                if (!$found) {
                    switch ($rule['rule']) {
                        case self::RULE_TAGS_STARTING_WITH_COUNT_GREATER_THAN:
                            if ($classificationResult->getTagsStartingWithString($rule['tag'], $rule['count'])) $found = true;
                            break;
                        case self::RULE_HAS_TAGS:
                            if ($classificationResult->hasTag($rule['tag'])) $found = true;
                            break;
                        default:
                            throw new \RuntimeException('A rule "' . $rule['rule'] . '" was used that does not exist.');
                    }
                }
            }

            if ($found) {
                $this->increaseCount($personaIdentifier);
                $this->addUrl((string)$classificationResult->getUri(), $personaIdentifier);
            }
        }
    }
}
