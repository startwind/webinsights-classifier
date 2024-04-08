<?php

namespace Startwind\WebInsights\Aggregation\Configuration;

use Psr\Log\LoggerAwareInterface;
use Startwind\WebInsights\Aggregation\Exporter\CompositeExporter;
use Startwind\WebInsights\Aggregation\Exporter\Exporter;
use Startwind\WebInsights\Aggregation\Retriever\Retriever;
use Startwind\WebInsights\Classification\Exception\CriticalException;
use Startwind\WebInsights\Configuration\Configuration;
use Startwind\WebInsights\Configuration\InheritanceAwareConfiguration;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class AggregationConfiguration extends Configuration implements InheritanceAwareConfiguration
{
    public const DEFAULT_CONFIG = __DIR__ . '/../../../config/aggregation/aggregation.config.yml';

    private const SECTION_RETRIEVER = 'retriever';
    private const SECTION_AGGREGATORS = 'aggregators';
    private const SECTION_EXPORTER = 'exporter';

    private Retriever $retriever;

    /** @var \Startwind\WebInsights\Aggregation\Aggregator\Aggregator[] */
    private array $aggregators;
    private Exporter $exporter;

    public function __construct(array $configArray, OutputInterface $output)
    {
        parent::__construct($configArray, $output);

        $this->initRetriever();
        $this->initAggregators();
        $this->initExporters();
    }

    private function initExporters(): void
    {
        $section = $this->getSection(self::SECTION_EXPORTER);

        $exporter = new CompositeExporter();

        if (!array_is_list($section)) {
            $section = [$section];
        }

        foreach ($section as $singleExporter) {
            $exporter->addExporter($this->initObject($singleExporter, Exporter::class));
        }

        $this->exporter = $exporter;
    }

    private function initRetriever(): void
    {
        /** @var Retriever $retriever */
        $retriever = $this->initObject($this->getSection(self::SECTION_RETRIEVER), Retriever::class);
        $this->retriever = $retriever;
    }

    public function initAggregators(): void
    {
        $classNames = $this->getSection(self::SECTION_AGGREGATORS);

        foreach ($classNames as $classifierElement) {
            if (is_array($classifierElement)) {
                $className = array_key_first($classifierElement);
                $parameters = $classifierElement[$className]['options'];
            } else {
                $className = $classifierElement;
                $parameters = [];
            }

            if (is_null($className)) continue;

            if (!class_exists($className)) {
                throw new CriticalException('No aggregator with class name "' . $className . '" found.');
            }

            $aggregator = new $className($parameters);

            if ($aggregator instanceof LoggerAwareInterface) {
                $aggregator->setLogger($this->logger);
            }

            $this->aggregators[] = $aggregator;
        }

    }

    /**
     * @return \Startwind\WebInsights\Aggregation\Aggregator\Aggregator[]
     */
    public function getAggregators(): array
    {
        return $this->aggregators;
    }

    public function getRetriever(): Retriever
    {
        return $this->retriever;
    }

    public function getExporter(): Exporter
    {
        return $this->exporter;
    }

    public static function fromFilename(string $filename, OutputInterface $output): static
    {
        $asArray = Yaml::parse(file_get_contents($filename));

        return self::fromArray($asArray, $output);
    }

    public static function fromArray(array $asArray, OutputInterface $output): static
    {
        $mandatorySections = [self::SECTION_RETRIEVER];

        if (is_subclass_of(static::class, InheritanceAwareConfiguration::class)) {
            if (array_key_exists(InheritanceAwareConfiguration::DEFAULT_SECTION, $asArray)
                && array_key_exists(InheritanceAwareConfiguration::DEFAULT_FIELD, $asArray[InheritanceAwareConfiguration::DEFAULT_SECTION])) {

                $defaultConfig = Yaml::parse(file_get_contents(self::DEFAULT_CONFIG));

                if (!array_key_exists(self::SECTION_EXPORTER, $asArray)) {
                    $asArray[self::SECTION_EXPORTER] = $defaultConfig[self::SECTION_EXPORTER];
                }

                if (!array_key_exists(self::SECTION_RETRIEVER, $asArray)) {
                    $asArray[self::SECTION_RETRIEVER] = $defaultConfig[self::SECTION_RETRIEVER];
                }

                if (array_key_exists(self::SECTION_AGGREGATORS, $asArray)) {
                    $asArray[self::SECTION_AGGREGATORS] = array_merge($defaultConfig[self::SECTION_AGGREGATORS], $asArray[self::SECTION_AGGREGATORS]);
                } else {
                    $asArray[self::SECTION_AGGREGATORS] = $defaultConfig[self::SECTION_AGGREGATORS];
                }

                foreach ($mandatorySections as $mandatorySection) {
                    if (!array_key_exists($mandatorySection, $asArray)) {
                        $asArray[$mandatorySection] = $defaultConfig[$mandatorySection];
                    }
                }
            }
        }

        return new self($asArray, $output);
    }
}
