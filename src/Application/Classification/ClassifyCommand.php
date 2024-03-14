<?php

namespace Startwind\WebInsights\Application\Classification;

use GuzzleHttp\Psr7\Uri;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'classify',
    description: 'Classify the given website or email address.',
    hidden: false
)]
class ClassifyCommand extends ClassificationCommand
{
    protected function configure()
    {
        $this->addOption(self::OPTION_CONFIG_FILE, 'c', InputOption::VALUE_OPTIONAL, 'The config file for the classifier', self::DEFAULT_CONFIG_FILE);
        $this->addArgument(self::ARGUMENT_CLASSIFY_STRING, InputArgument::REQUIRED, 'The string that should be classified (email and url supported).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input, $output);

        $string = $this->repairString($input->getArgument(self::ARGUMENT_CLASSIFY_STRING));

        $this->initRetriever();
        $this->getRetriever()->setUris([new Uri($string)]);

        $response = $this->getRetriever()->next();

        if ($response) {
            $classificationResult = $this->classify($response);
        } else {
            $output->writeln('<error>Unable to request "' . $string . '".');
            return Command::FAILURE;
        }

        $this->getExporter()->export($classificationResult);
        $this->getExporter()->finish();

        return Command::SUCCESS;
    }
}
