<?php

namespace Startwind\WebInsights\Application\Enrich;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LogLevel;
use Startwind\WebInsights\Application\Classification\ClassificationCommand;
use Startwind\WebInsights\Hosting\AS\CompositeAsExtractor;
use Startwind\WebInsights\Hosting\AS\CymruAsExtractor;
use Startwind\WebInsights\Hosting\Export\ApiExporter;
use Startwind\WebInsights\Hosting\IpRange\CompositeIpRangeExtractor;
use Startwind\WebInsights\Hosting\IpRange\HackerTargetIpRangeExtractor;
use Startwind\WebInsights\Hosting\IpRange\RadbWhoisIpRangeExtractor;
use Startwind\WebInsights\Logger\OutputLogger;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'enrich:feed',
    description: 'Return the IP range of the AS hosting a specific domain',
    hidden: false
)]
class RssFeedCommand extends ClassificationCommand
{
    const ARGUMENT_DOMAIN = 'domain';

    protected function configure(): void
    {
        $this->addArgument(self::ARGUMENT_DOMAIN, InputArgument::REQUIRED, 'The domain that should be used to return the IP range of the ISP/AS.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new OutputLogger(LogLevel::WARNING);
        $logger->setOutput($output);

        $domain = $input->getArgument(self::ARGUMENT_DOMAIN);



        return Command::SUCCESS;
    }
}
