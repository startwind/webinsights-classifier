<?php

namespace Startwind\WebInsights\Application\Aggregation;

use Startwind\WebInsights\Aggregation\Configuration\AggregationConfiguration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'aggregate',
    description: 'Aggregate classified websites.',
    hidden: false
)]
class AggregateCommand extends AggregationCommand
{
    protected function configure(): void
    {
        $this->addOption(self::OPTION_CONFIG_FILE, 'c', InputOption::VALUE_OPTIONAL, 'The config file for the classifier', AggregationConfiguration::DEFAULT_CONFIG);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configuration = AggregationConfiguration::fromFilename(
            $input->getOption(self::OPTION_CONFIG_FILE), $output
        );

        $this->doExecute();

        return Command::SUCCESS;
    }
}
