<?php

namespace Startwind\WebInsights\Response\Enricher;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Startwind\WebInsights\Response\HttpResponse;

class GeoLocationEnricher implements Enricher, ManyEnricher
{
    const VERSION = "1";

    const SERVICE_URL = 'http://ip-api.com/json/';
    const SERVICE_URL_BATCH = 'http://ip-api.com/batch/';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function enrich(HttpResponse $response): array|false
    {
        $this->enrichMany([$response]);
        if ($response->hasEnrichment(self::getIdentifier())) {
            return $response->getEnrichment(self::getIdentifier());
        } else {
            return false;
        }
    }

    /**
     * @param HttpResponse[] $responses
     */
    public function enrichMany(array $responses): void
    {
        $ips = [];
        $ip2key = [];

        foreach ($responses as $key => $response) {
            if ($response->getServerIP()) {
                $ip = $response->getServerIP();
            } else {
                $ip = $response->getRequestUri()->getHost();
            }
            $ip2key[$key] = $ip;
            $ips[] = $ip;
        }

        $ips = array_values(array_unique($ips));

        $response = $this->client->post(self::SERVICE_URL_BATCH, [
            RequestOptions::TIMEOUT => 10,
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::JSON => $ips
        ]);

        $results = json_decode((string)$response->getBody(), true);

        $data = [];

        foreach ($results as $result) {
            $data[$result['query']] = $result;
        }

        foreach ($responses as $key => $response) {
            if (array_key_exists($key, $ip2key)) {
                $ip = $ip2key[$key];
                if (array_key_exists($ip, $data)) {
                    $response->enrich(self::getIdentifier(), $data[$ip]);
                }
            }
        }
    }

    static public function getIdentifier(): string
    {
        return self::class . '_' . self::VERSION;
    }
}
