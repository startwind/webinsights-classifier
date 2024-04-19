<?php

namespace Startwind\WebInsights\Hosting\Export;

use Symfony\Component\Console\Output\OutputInterface;

class OutputExporter implements Exporter
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function export(string $as, array $ipRanges): void
    {
        foreach ($ipRanges as $ipRange) {
            $range = '';
            $mongoDb = '';

            if (str_contains($ipRange, '.')) {
                $subnetParts = explode('/', $ipRange);
                $size = pow(2, 32 - $subnetParts[1]);

                $ipStart = $subnetParts[0];
                $ipEnd = long2ip(ip2long($subnetParts[0]) + $size - 1);

                $range = ' (' . ip2long($ipStart) . ' - ' . ip2long($ipEnd) . ')';
                $mongoDb = ' - { ip_long: { $gt: ' . ip2long($ipStart) . ', $lt: ' . ip2long($ipEnd) . ' } }' . ' - ';
            }

            $this->output->writeln(' - ' . $ipRange . $range . $mongoDb);
        }
    }
}
