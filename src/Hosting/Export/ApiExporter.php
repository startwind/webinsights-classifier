<?php

namespace Startwind\WebInsights\Hosting\Export;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ApiExporter implements Exporter
{
    const API = 'https://api.webinsights.info/as/';

    public function export(string $as, array $ipRanges): void
    {
        $ranges = [];

        foreach ($ipRanges as $ipRange) {
            if (str_contains($ipRange, '.')) {
                $subnetParts = explode('/', $ipRange);
                $size = pow(2, 32 - $subnetParts[1]);

                $ipStart = $subnetParts[0];
                $ipEnd = long2ip(ip2long($subnetParts[0]) + $size - 1);

                if (!$ipStart) continue;
                if (!$ipEnd) continue;

                $ranges[] = [
                    'from' => ip2long($ipStart),
                    'to' => ip2long($ipEnd)
                ];
            }
        }

        $client = new Client();

        $client->post(self::API . $as, [RequestOptions::JSON => ['ipRanges' => $ranges]]);
    }
}
