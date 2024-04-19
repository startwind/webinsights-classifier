<?php

namespace Startwind\WebInsights\Application\Hosting;

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
    name: 'hosting:as:iprange',
    description: 'Return the IP range of the AS hosting a specific domain',
    hidden: false
)]
class GetIpRangeCommand extends ClassificationCommand
{
    const ARGUMENT_DOMAIN = 'domain';

    protected function configure(): void
    {
        $this->addArgument(self::ARGUMENT_DOMAIN, InputArgument::OPTIONAL, 'The domain that should be used to return the IP range of the ISP/AS.', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new OutputLogger(LogLevel::WARNING);
        $logger->setOutput($output);

        $domain = $input->getArgument(self::ARGUMENT_DOMAIN);

        if (!$domain) {
            $client = new Client();

            try {
                $response = $client->get('https://api.webinsights.info/as/job/pop/');
            } catch (ClientException $exception) {
                $output->writeln('<error>' . $exception->getMessage() . '</error>');
                return Command::FAILURE;
            }

            $result = json_decode($response->getBody(), true);

            $job = $result['data']['job'];

            if (array_key_exists('as', $job)) {
                $domain = $job['as'];
            } else {
                $domain = $job['domain'];
            }
        }

        $exporter = new ApiExporter();

        if (!is_numeric($domain)) {
            $asExtractor = new CompositeAsExtractor();
            $asExtractor->setLogger($logger);

            $asExtractor->addExtractor(new CymruAsExtractor());

            $domain = str_replace('https://', '', $domain);
            $domain = str_replace('http://', '', $domain);

            $as = $asExtractor->getAs($domain);
        } else {
            $as = $domain;
        }

        $output->writeln('Autonomous System for ' . $domain . ' is AS' . $as);

        $ipRangeExtractor = new CompositeIpRangeExtractor();
        $ipRangeExtractor->setLogger($logger);

        $ipRangeExtractor->addExtractor(new HackerTargetIpRangeExtractor());
        $ipRangeExtractor->addExtractor(new RadbWhoisIpRangeExtractor());

        $ipRanges = $ipRangeExtractor->getIpRange($as);

        $ipCount = 0;

        foreach ($ipRanges as $ipRange) {
            if (str_contains($ipRange, '.')) {
                $subnetParts = explode('/', $ipRange);
                $size = pow(2, 32 - $subnetParts[1]);
                $ipCount += $size;
            }
        }

        $exporter->export($as, $ipRanges, $domain);

        $output->writeln('');
        $output->writeln('');
        $output->writeln("Found <info>" . count($ipRanges) . " IP ranges</info> containing of <info>" . number_format($ipCount) . " IPv4 addresses</info>.");

        return Command::SUCCESS;
    }
}
