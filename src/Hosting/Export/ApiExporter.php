<?php

namespace Startwind\WebInsights\Hosting\Export;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ApiExporter implements Exporter
{
    const API = 'https://api.webinsights.info/as/';
    const API_MANY = 'https://api.webinsights.info/as/many/';

    public function export(string $as, array $ipRanges, string $domain = '', $handle = '', $description = ''): void
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

        $client->post(self::API . $as, [RequestOptions::JSON => ['ipRanges' => $ranges, 'domain' => $domain, 'handle' => $handle, 'description' => $description]]);
    }

    public function exportMany(array $asArray): void
    {
        $client = new Client();

        $payload = [];

        foreach ($asArray as $as => $data) {

            $ipRanges = $data['ipRanges'];

            if (array_key_exists('handle', $data)) {
                $handle = $data['handle'];
            } else {
                $handle = '';
            }

            if (array_key_exists('description', $data)) {
                $description = $data['description'];
            } else {
                $description = '';
            }

            if (array_key_exists('domain', $data)) {
                $domain = $data['domain'];
            } else {
                $domain = '';
            }

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

            $payload[$as] = ['ipRanges' => $ranges, 'domain' => $domain, 'handle' => $handle, 'description' => $description];
        }

        $client->post(self::API . $as, [RequestOptions::JSON => ['asSet' => $payload]]);
    }
}

;
