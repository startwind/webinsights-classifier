<?php

namespace Startwind\WebInsights\Application\Classification;

use Startwind\WebInsights\Classification\Exception\EmptyQueueException;
use Startwind\WebInsights\Classification\Feeder\FileFeeder;
use Startwind\WebInsights\Configuration\Resume;
use Startwind\WebInsights\Util\Timer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'classifyMany',
    description: 'Classify a list of websites',
    hidden: false
)]
class ClassifyManyCommand extends ClassificationCommand
{
    private const OPTION_CLASSIFICATION_LIMIT = 'classificationLimit';
    private const OPTION_RESUME_FILE = 'resumeFile';
    private const OPTION_RUN_NAME = 'runName';

    private const LIMIT_UNLIMITED = '-1';

    protected OutputInterface $output;

    protected function configure()
    {
        $this->addOption(self::OPTION_CONFIG_FILE, 'c', InputOption::VALUE_OPTIONAL, 'The config file for the classifier', self::DEFAULT_CONFIG_FILE);

        $this->addOption(self::OPTION_CLASSIFICATION_LIMIT, 'l', InputOption::VALUE_OPTIONAL, 'The config file for the classifier', self::LIMIT_UNLIMITED);
        $this->addOption(self::OPTION_RESUME_FILE, 'r', InputOption::VALUE_OPTIONAL, 'The resume file');
        $this->addOption(self::OPTION_RUN_NAME, 't', InputOption::VALUE_OPTIONAL, 'The name of the run.');

        $this->addArgument(self::ARGUMENT_CLASSIFY_FILE, InputArgument::OPTIONAL, 'The file with the urls to be classified.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $timer = new Timer();

        //pcntl_signal(SIGTERM, [&$this, 'shutdown']);
        //pcntl_signal(SIGCHLD, [&$this, 'shutdown']);
        //pcntl_signal(SIGINT, array(&$this, 'shutdown'));

        if ($input->getOption(self::OPTION_RESUME_FILE)) {
            $resumeFile = $input->getOption(self::OPTION_RESUME_FILE);
            $resumeConfig = Yaml::parse(file_get_contents($resumeFile));

            $this->setRunId($resumeConfig['runId']);

            $this->init($input, $output, $resumeConfig['config']);
            $filename = $resumeConfig['inputFile'];
            $startWith = $resumeConfig['position'];

            $this->configuration->setFeeder(new FileFeeder($filename));
        } else {
            if ($input->getOption(self::OPTION_RUN_NAME)) {
                $this->setRunId($input->getOption(self::OPTION_RUN_NAME));
            }
            try {
                $this->init($input, $output);
            } catch (EmptyQueueException) {
                $output->writeln('<error>Queue is empty. Sleeping for 1 second.');
                sleep(1);
                return Command::FAILURE;
            }
            $startWith = 0;
        }

        $limit = $input->getOption(self::OPTION_CLASSIFICATION_LIMIT);

        $this->initRetriever($limit, $startWith);

        $feeder = $this->configuration->getFeeder();

        if ($feeder instanceof FileFeeder) {
            $this->resume = new Resume($this->getRunId(), $this->config, $feeder->getFilename());
        } else {
            $this->resume = new Resume($this->getRunId(), $this->config, '');
        }

        try {
            $domainContainer = $feeder->getDomainContainer();
        } catch (\Exception $exception) {
            $output->writeln(['', '<error> ' . $exception->getMessage() . ' </error>', '']);
            return Command::FAILURE;
        }

        $this->getLogger()->info('Start classifying ' . $domainContainer->getCount() . ' URLS.');

        $retriever = $this->getRetriever();

        $retriever->setUris($domainContainer->toUriList());

        while ($httpResponse = $retriever->next()) {
            $memoryUsed = (int)(memory_get_usage() / 1000 / 1000);
            if ($memoryUsed > 256) {
                $this->getLogger()->alert('Processing next response. Memory usage: ' . $memoryUsed . ' MB.');
            } else {
                $this->getLogger()->debug('Processing next response. Memory usage: ' . $memoryUsed . ' MB.');
            }

            $tags = $this->classify($httpResponse);

            $predefinedTags = $domainContainer->getDomain((string)$httpResponse->getRequestUri())->getTags();

            foreach ($predefinedTags as $predefinedTag) {
                $tags->addTag($predefinedTag);
            }

            $this->getExporter()->export($tags);

            file_put_contents('/var/log/webinsights/requests.log', date('Y-m-d H:i:s') . ' - ' . (string)$httpResponse->getRequestUri() . PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        $exporter = $this->getExporter();

        $this->configuration->getStorage()->finish();

        $this->outputExportSummary($exporter->finish());

        $this->getLogger()->alert('Classification took ' . $timer->getTimePassed(Timer::UNIT_SECONDS) . ' seconds.');

        return Command::SUCCESS;
    }
}
