<?php

namespace Startwind\WebInsights\Application\Hosting;

use Psr\Log\LogLevel;
use Startwind\WebInsights\Application\Classification\ClassificationCommand;
use Startwind\WebInsights\Hosting\AS\CompositeAsExtractor;
use Startwind\WebInsights\Hosting\AS\CymruAsExtractor;
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
    name: 'hosting:as:iprange',
    description: 'Return the IP range of the AS hosting a specific domain',
    hidden: false
)]
class GetIpRangeCommand extends ClassificationCommand
{
    const ARGUMENT_DOMAIN = 'domain';

    protected function configure(): void
    {
        $this->addArgument(self::ARGUMENT_DOMAIN, InputArgument::OPTIONAL, 'The domain that should be used to return the IP range of the ISP/AS.', 'www.koality.io');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new OutputLogger(LogLevel::WARNING);
        $logger->setOutput($output);

        $asExtractor = new CompositeAsExtractor();
        $asExtractor->setLogger($logger);

        $asExtractor->addExtractor(new CymruAsExtractor());

        $domain = $input->getArgument(self::ARGUMENT_DOMAIN);

        $as = $asExtractor->getAs($domain);

        $output->writeln('Autonomous System for ' . $domain . ' is AS' . $as);

        $ipRangeExtractor = new CompositeIpRangeExtractor();
        $ipRangeExtractor->setLogger($logger);

        $ipRangeExtractor->addExtractor(new HackerTargetIpRangeExtractor());
        $ipRangeExtractor->addExtractor(new RadbWhoisIpRangeExtractor());

        $ipRanges = $ipRangeExtractor->getIpRange($as);

        $ipCount = 0;

        foreach ($ipRanges as $ipRange) {
            $range = '';

            if (str_contains($ipRange, '.')) {
                $subnetParts = explode('/', $ipRange);
                $size = pow(2, 32 - $subnetParts[1]);

                $ipCount += $size;

                $ipStart = $subnetParts[0];
                $ipEnd = long2ip(ip2long($subnetParts[0]) + $size - 1);

                // $range = ' (' . ip2long($ipStart) . ' - ' . ip2long($ipEnd) . ')';
            }

            $output->writeln(' - ' . $ipRange . $range);
        }

        $output->writeln('');
        $output->writeln('');
        $output->writeln("Found <info>" . count($ipRanges) . " IP ranges</info> containing of <info>" . number_format($ipCount) . " IPv4 addresses</info>.");

        return Command::SUCCESS;
    }
}
