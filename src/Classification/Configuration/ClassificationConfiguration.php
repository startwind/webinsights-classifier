<?php

namespace Startwind\WebInsights\Classification\Configuration;

use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerAwareInterface;
use Startwind\WebInsights\Classification\Classifier\LoggerAwareClassifier;
use Startwind\WebInsights\Classification\Exception\CriticalException;
use Startwind\WebInsights\Classification\Exception\EmptyQueueException;
use Startwind\WebInsights\Classification\Feeder\Feeder;
use Startwind\WebInsights\Configuration\Configuration;
use Startwind\WebInsights\Configuration\Exception\ConfigurationException;
use Startwind\WebInsights\Response\Retriever\EnrichmentAwareRetriever;
use Startwind\WebInsights\Response\Retriever\HttpClientAwareRetriever;
use Startwind\WebInsights\Response\Retriever\Retriever;
use Startwind\WebInsights\Response\Retriever\StorageAwareRetriever;
use Startwind\WebInsights\Storage\NullStorage;
use Startwind\WebInsights\Storage\Storage;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ClassificationConfiguration extends Configuration
{
    public const SECTION_EXPORTER = 'exporter';
    public const SECTION_CLASSIFIER = 'classifiers';
    public const SECTION_ENRICHER = 'enricher';
    public const SECTION_RETRIEVER = 'retriever';
    public const SECTION_STORAGE = 'storage';
    public const SECTION_FEEDER = 'feeder';

    public const DEFAULT_CONFIG_FILE = __DIR__ . '/../../../config/config.yml';

    private static array $mandatorySections = [
        self::SECTION_RETRIEVER, self::SECTION_CLASSIFIER, self::SECTION_EXPORTER, self::SECTION_GENERAL
    ];

    private Storage $storage;

    /**
     * @var \Startwind\WebInsights\Response\Enricher\Enricher[]
     */
    private array $enrichers = [];

    private Retriever $retriever;

    private ClientInterface $client;

    /**
     * @var \Startwind\WebInsights\Classification\Classifier\Classifier[]
     */
    private array $classifiers = [];
    private ?Feeder $feeder = null;

    public function __construct(array $configurationArray, ClientInterface $client, OutputInterface $output)
    {
        parent::__construct($configurationArray, $output);

        $this->client = $client;

        $this->initFeeder();
        $this->initStorage();
        $this->initEnricher();
        $this->initRetriever();
        $this->initClassifiers();
    }

    private function initClassifiers(): void
    {
        $classNames = $this->getSection(ClassificationConfiguration::SECTION_CLASSIFIER);

        foreach ($classNames as $classifierElement) {
            if (is_array($classifierElement)) {
                $className = array_key_first($classifierElement);
                $parameters = $classifierElement[$className];
            } else {
                $className = $classifierElement;
                $parameters = [];
            }

            if (is_null($className)) continue;

            if (!class_exists($className)) {
                throw new CriticalException('No classifier with class name "' . $className . '" found.');
            }

            $classifier = new $className();

            if (count($parameters)) {
                if (method_exists($classifier, 'init')) {
                    $classifier->init($parameters);
                } else {
                    throw new \RuntimeException('For classifier "' . $className . '" there a parameters defined in the config file, but no init method available for this class.');
                }
            }

            if ($classifier instanceof LoggerAwareClassifier) {
                $classifier->setLogger($this->logger);
            }

            $this->classifiers[] = $classifier;
        }
    }

    private function initRetriever(): void
    {
        /** @var Retriever $retriever */
        $retriever = $this->initObject($this->getSection(self::SECTION_RETRIEVER), Retriever::class);
        $this->retriever = $retriever;

        if ($this->retriever instanceof StorageAwareRetriever) {
            $this->retriever->setStorage($this->getStorage());
        }

        if ($this->retriever instanceof HttpClientAwareRetriever) {
            $this->retriever->setHttpClient($this->client);
        }

        if ($this->retriever instanceof EnrichmentAwareRetriever) {
            foreach ($this->enrichers as $enricher) {
                $this->retriever->addEnricher($enricher);
            }
        }
    }

    private function initFeeder(): void
    {
        if ($this->hasSection(self::SECTION_FEEDER)) {
            /** @var Feeder $feeder */
            $feeder = $this->initObject($this->getSection(self::SECTION_FEEDER), Feeder::class);

            if ($feeder instanceof LoggerAwareInterface) {
                $feeder->setLogger($this->logger);
            }

            $this->feeder = $feeder;
        }
    }

    public function hasFeeder(): bool
    {
        return !is_null($this->feeder);
    }

    public function setFeeder(Feeder $feeder): void
    {
        $this->feeder = $feeder;
    }

    public function getFeeder(): ?Feeder
    {
        return $this->feeder;
    }

    private function initStorage(): void
    {
        if ($this->hasSection(self::SECTION_STORAGE)) {
            /** @var Storage $storage */
            $storage = $this->initObject($this->getSection(self::SECTION_STORAGE), Storage::class);
            $this->storage = $storage;
        } else {
            $this->storage = new NullStorage();
        }
    }

    private function initEnricher(): void
    {
        if ($this->hasSection(ClassificationConfiguration::SECTION_ENRICHER)) {
            $enrichers = $this->getSection(ClassificationConfiguration::SECTION_ENRICHER);

            foreach ($enrichers as $enricher) {
                $this->enrichers[] = new $enricher();
            }
        }
    }

    public function getStorage(): Storage
    {
        return $this->storage;
    }

    public function getRetriever(): Retriever
    {
        return $this->retriever;
    }

    /**
     * @return \Startwind\WebInsights\Classification\Classifier\Classifier[]
     */
    public function getClassifiers(): array
    {
        return $this->classifiers;
    }


    static public function fromFilename(string $filename, ClientInterface $client, OutputInterface $output): self
    {
        if (!str_starts_with($filename, 'http')) {
            if (!file_exists($filename)) {
                throw new ConfigurationException('The given config file does not exist. Filename: ' . $filename);
            }
        }

        $asArray = Yaml::parse(file_get_contents($filename));

        if (array_key_exists('status', $asArray) && ($asArray['status'] === 'failures' || $asArray['status'] === 'failure')) {
            throw new EmptyQueueException('Queue is empty.');
        }

        if (array_key_exists(self::SECTION_GENERAL, $asArray)
            && array_key_exists('inherit_default', $asArray[self::SECTION_GENERAL])
            && $asArray['general']['inherit_default']) {

            $defaultConfig = Yaml::parse(file_get_contents(self::DEFAULT_CONFIG_FILE));

            if (!array_key_exists(self::SECTION_STORAGE, $asArray)) {
                $asArray[self::SECTION_STORAGE] = $defaultConfig[self::SECTION_STORAGE];
            }

            if (!array_key_exists(self::SECTION_ENRICHER, $asArray)) {
                $asArray[self::SECTION_ENRICHER] = $defaultConfig[self::SECTION_ENRICHER];
            }

            if (!array_key_exists(self::SECTION_RETRIEVER, $asArray)) {
                $asArray[self::SECTION_RETRIEVER] = $defaultConfig[self::SECTION_RETRIEVER];
            }

            if (!array_key_exists(self::SECTION_GENERAL, $asArray) || !array_key_exists('log_level', $asArray[self::SECTION_GENERAL])) {
                $asArray[self::SECTION_GENERAL]['log_level'] = $defaultConfig[self::SECTION_GENERAL]['log_level'];
            }

            if (array_key_exists(self::SECTION_EXPORTER, $asArray)) {
                $asArray[self::SECTION_EXPORTER] = array_merge($defaultConfig[self::SECTION_EXPORTER], $asArray[self::SECTION_EXPORTER]);
            } else {
                $asArray[self::SECTION_EXPORTER] = $defaultConfig[self::SECTION_EXPORTER];
            }

            if (array_key_exists(self::SECTION_CLASSIFIER, $asArray)) {
                $asArray[self::SECTION_CLASSIFIER] = array_merge($defaultConfig[self::SECTION_CLASSIFIER], $asArray[self::SECTION_CLASSIFIER]);
            } else {
                $asArray[self::SECTION_CLASSIFIER] = $defaultConfig[self::SECTION_CLASSIFIER];
            }
        }

        self::assertConfigArrayValid($asArray);

        return new self($asArray, $client, $output);
    }

    private static function assertConfigArrayValid(array $configArray): void
    {
        foreach (self::$mandatorySections as $mandatoryKey) {
            if (!array_key_exists($mandatoryKey, $configArray)) {
                throw new ConfigurationException('Mandatory config key "' . $mandatoryKey . '" missing.');
            }
        }
    }
}
