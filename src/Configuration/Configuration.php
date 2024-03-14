<?php

namespace Startwind\WebInsights\Configuration;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Startwind\WebInsights\Configuration\Exception\ConfigurationException;
use Startwind\WebInsights\Logger\CompositeLogger;
use Startwind\WebInsights\Logger\FileLogger;
use Startwind\WebInsights\Logger\OutputLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @todo This can be a separate open source project
 */
class Configuration
{
    protected string $initializableClassName = Initializable::DEFAULT_FIELD_CLASS;
    protected string $initializableOptionsName = Initializable::DEFAULT_FIELD_OPTIONS;

    public const SECTION_GENERAL = 'general';

    public const SECTION_LOGGER = 'logger';

    protected LoggerInterface $logger;
    private array $configurationArray;

    private OutputInterface $output;

    public function __construct(array $configurationArray, OutputInterface $output)
    {
        $this->configurationArray = $configurationArray;
        $this->output = $output;

        $this->initLogger();
    }

    public function getConfigurationArray(): array
    {
        return $this->configurationArray;
    }

    public function hasSection(string $section): bool
    {
        return array_key_exists($section, $this->configurationArray);
    }

    public function getSection(string $section): array
    {
        return $this->configurationArray[$section];
    }

    public function hasOption(string $section, $key): bool
    {
        return $this->hasSection($section) && array_key_exists($key, $this->configurationArray[$section]);
    }

    public function getOption(string $section, $key): mixed
    {
        return $this->configurationArray[$section][$key];
    }

    protected function initLogger(): void
    {
        if ($this->hasOption(self::SECTION_GENERAL, 'log_level')) {
            $logLevel = $this->getOption(self::SECTION_GENERAL, 'log_level');
        } else {
            $logLevel = LogLevel::ALERT;
        }

        $this->logger = new CompositeLogger();

        $this->logger->addLogger(new OutputLogger($logLevel));
        $this->logger->addLogger(new FileLogger($logLevel, '_log/classifier.log'));

        $this->logger->setOutput($this->output);
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function initObject(array $objectConfigArray, string $returnedClassName): mixed
    {
        if (!array_key_exists($this->initializableClassName, $objectConfigArray)) {
            throw new ConfigurationException('The mandatory configuration field "' . $this->initializableClassName . '" is missing.');
        }

        $className = $objectConfigArray[$this->initializableClassName];

        if (array_key_exists($this->initializableOptionsName, $objectConfigArray)) {
            $options = $objectConfigArray[$this->initializableOptionsName];
        } else {
            $options = [];
        }

        $object = new $className($options);

        if (!$object instanceof $returnedClassName) {
            throw new ConfigurationException('The created object is not an instance of "' . $returnedClassName . '". Given:' . get_class($object) . '.');
        }

        if ($object instanceof LoggerAwareInterface) {
            $object->setLogger($this->logger);
        }

        return $object;
    }
}
