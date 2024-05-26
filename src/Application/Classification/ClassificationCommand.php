<?php

namespace Startwind\WebInsights\Application\Classification;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\ExtrasClassifier;
use Startwind\WebInsights\Classification\Configuration\ClassificationConfiguration;
use Startwind\WebInsights\Classification\Exception\CriticalException;
use Startwind\WebInsights\Classification\Exporter\CompositionExporter;
use Startwind\WebInsights\Classification\Exporter\Exporter;
use Startwind\WebInsights\Classification\Exporter\OutputAwareExporter;
use Startwind\WebInsights\Classification\Feeder\DomainListFeeder;
use Startwind\WebInsights\Classification\Feeder\FileFeeder;
use Startwind\WebInsights\Configuration\Configuration;
use Startwind\WebInsights\Configuration\Exception\ConfigurationException;
use Startwind\WebInsights\Configuration\Resume;
use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Response\Retriever\Retriever;
use Startwind\WebInsights\Storage\RunIdAwareStorage;
use Startwind\WebInsights\Util\Timer;
use Startwind\WebInsights\Util\UrlHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

abstract class ClassificationCommand extends Command
{
    public const RUN_ID_PREFIX = '--';

    protected const ARGUMENT_CLASSIFY_FILE = 'classifyFile';
    protected const ARGUMENT_CLASSIFY_STRING = 'classifyString';

    protected const OPTION_CONFIG_FILE = 'configFile';
    protected const DEFAULT_CONFIG_FILE = __DIR__ . '/../../../config/config.yml';

    protected array $config;

    /**
     * @var Classifier[]
     */
    protected array $classifiers = [];

    private Exporter $exporter;

    protected Resume $resume;

    private Client $client;

    private string $runId;

    private Retriever $retriever;

    private LoggerInterface $logger;

    protected OutputInterface $output;

    protected int $parallelRequests = 5;

    protected ClassificationConfiguration $configuration;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->setRunId(self::RUN_ID_PREFIX . time());
    }

    protected function setRunId(string $runId): void
    {
        $this->runId = $runId;
    }

    protected function getRunId(): string
    {
        return $this->runId;
    }

    protected function classify(HttpResponse $response): ClassificationResult
    {
        $timer = new Timer();

        if ($response->hasEffectiveUri()) {
            $uri = $response->getEffectiveUri();
        } else {
            $uri = $response->getRequestUri();
        }

        $classificationResult = new ClassificationResult(UrlHelper::rootUri($uri));

        foreach ($this->classifiers as $classifier) {
            try {
                $timer->start();
                $tags = $classifier->classify($response, $classificationResult->getTags());
                if ($classifier instanceof ExtrasClassifier) {
                    $classificationResult->addTags($tags, false);
                } else {
                    $classificationResult->addTags($tags, true);
                }
                $time = $timer->getTimePassed();
                $this->logger->debug('Classification completed in ' . $time . ' ms. Classifier: ' . get_class($classifier) . '.');
                if ($time > 100) {
                    $this->logger->alert('Classification slow: ' . $time . ' ms. Classifier: ' . get_class($classifier) . '.');
                }
            } catch (CriticalException $e) {
                $this->output->writeln('<error>' . $e->getMessage() . '</error>');
                break;
            } catch (\Exception $e) {
                $this->output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }

        return $classificationResult;
    }

    /**
     * @todo move this to configuration class
     */
    private function initExporter()
    {
        $exporterConfig = $this->configuration->getSection(ClassificationConfiguration::SECTION_EXPORTER);

        $compositeExporter = new CompositionExporter();

        foreach ($exporterConfig as $exporterElement) {
            if (is_array($exporterElement)) {
                $className = array_key_first($exporterElement);
                $parameters = $exporterElement[$className];
            } else {
                $className = $exporterElement;
                $parameters = [];
            }

            try {
                if (!class_exists($className)) {
                    throw new ConfigurationException('Class not found.');
                }
                $exporter = new $className($this->runId, $parameters);
            } catch (ConfigurationException $e) {
                $this->logger->alert('Failure initializing exporter ' . $className . ': ' . $e->getMessage());
                continue;
            }

            if ($exporter instanceof OutputAwareExporter) {
                $exporter->setOutput($this->output);
            }

            $compositeExporter->addExporter($exporter);
        }

        $compositeExporter->setLogger($this->logger);

        $this->exporter = $compositeExporter;
    }

    protected function init(InputInterface $input, OutputInterface $output, array $config = [])
    {
        $this->output = $output;

        $this->initClient();

        if (empty($config)) {
            $this->initConfig($input->getOption(self::OPTION_CONFIG_FILE));
        } else {
            $this->initConfig($config);
        }

        if (!$this->configuration->hasFeeder()) {
            if ($input->hasArgument(self::ARGUMENT_CLASSIFY_FILE)) {
                $filename = $input->getArgument(self::ARGUMENT_CLASSIFY_FILE);

                if (!$filename) {
                    throw new CriticalException('Mandatory argument ' . self::ARGUMENT_CLASSIFY_FILE . ' is missing.');
                }
                $this->configuration->setFeeder(new FileFeeder($filename));
            } else {
                $this->configuration->setFeeder(new DomainListFeeder([$input->getArgument(self::ARGUMENT_CLASSIFY_STRING)]));
            }
        }

        $this->classifiers = $this->configuration->getClassifiers();
        $this->logger = $this->configuration->getLogger();

        $this->initStorage();

        $this->initClassifiers();
        $this->initExporter();
    }

    private function initConfig(string|array $config): void
    {
        if (is_string($config)) {
            $this->configuration = ClassificationConfiguration::fromFilename($config, $this->client, $this->output);
        } else {
            $this->configuration = new ClassificationConfiguration($config, $this->client, $this->output);
        }

        if ($this->configuration->hasOption(Configuration::SECTION_GENERAL, 'parallelRequests')) {
            $this->parallelRequests = $this->configuration->getOption(Configuration::SECTION_GENERAL, 'parallelRequests');
        }

        $this->config = $this->configuration->getConfigurationArray();
    }

    private function initClient()
    {
        $this->client = new Client();
    }

    private function initStorage(): void
    {
        $storage = $this->configuration->getStorage();

        if ($storage instanceof RunIdAwareStorage) {
            $storage->setRunId($this->runId);
        }
    }

    protected function initRetriever(int $limit = Retriever::LIMIT_UNLIMITED, int $startWith = 0)
    {
        $this->retriever = $this->configuration->getRetriever();

        $this->retriever->setLimit($limit);
        $this->retriever->setPosition($startWith);
    }

    private function initClassifiers(): void
    {
        $this->classifiers = $this->configuration->getClassifiers();
    }

    protected function getExporter(): Exporter
    {
        return $this->exporter;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getRetriever(): Retriever
    {
        return $this->retriever;
    }

    protected function outputExportSummary(string $result): void
    {
        $this->output->writeln(['', '', '<comment>Classification Summary</comment>', '']);
        $this->output->writeln($result);
    }

    protected function repairString(string $string): string
    {
        $string = trim($string);

        if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
            return $string;
        }

        if (!\str_starts_with($string, 'http')) {
            $string = 'https://' . $string;
        }
#
        return $string;
    }

    private function persistResume(): string
    {
        $this->resume->setPosition($this->getRetriever()->getPosition());

        $filename = Resume::FILE_PREFIX . $this->getRunId() . '.yml';
        file_put_contents($filename, Yaml::dump($this->resume->jsonSerialize(), 4));

        return $filename;
    }

    public function shutdown(): void
    {
        $this->outputExportSummary($this->getExporter()->finish());

        $this->output->writeln(['', '', '<error>                               ', '  Command manually canceled.   ', '                               </error>']);

        $resumeFile = $this->persistResume();

        $this->output->writeln(['', '  To resume this classification run:', '', '  <info>php bin/classifier.php classifyMany -r ' . $resumeFile . '</info>']);

        exit(Command::FAILURE);
    }
}
