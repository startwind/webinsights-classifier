<?php

namespace Startwind\WebInsights\Hosting\IpRange;

class RadbWhoisIpRangeExtractor implements IpRangeExtractor
{
    // https://radb.net/query?advanced_query=1&keywords=AS24940&-T+option=&ip_option=&-i=1&-i+option=origin

    public function getIpRange(string $as): array
    {
        $command = "whois -h whois.radb.net -- '-i origin AS" . $as . "' | grep 'route:'";

        exec($command, $output);

        $ipRanges = [];

        foreach ($output as $ipRangeLine) {
            $ipRanges[] = trim(str_replace('route:', '', $ipRangeLine));
        }

        return $ipRanges;
    }
}
