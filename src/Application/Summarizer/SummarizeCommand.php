<?php

namespace Startwind\WebInsights\Application\Summarizer;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'summarize',
    description: 'Summarize classified websites.',
    hidden: false
)]
class SummarizeCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
