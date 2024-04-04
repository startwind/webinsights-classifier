<?php

namespace Startwind\WebInsights\Hosting\IpRange;

use GuzzleHttp\Client;

class HackerTargetIpRangeExtractor implements IpRangeExtractor
{
    public function getIpRange(string $as): array
    {
        $url = "https://api.hackertarget.com/aslookup/?q=AS" . $as;

        $client = new Client();

        $response = $client->get($url);

        $ipListString = (string)$response->getBody();

        if (str_contains($ipListString, 'API count exceeded')) {
            throw new \RuntimeException('API count exceeded');
        }

        $ipListElements = explode("\n", $ipListString);

        array_shift($ipListElements);

        return $ipListElements;
    }
}
