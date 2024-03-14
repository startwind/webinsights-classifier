<?php

namespace Startwind\WebInsights\Response\Enricher;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\RequestOptions;
use Startwind\WebInsights\Response\HttpResponse;

class GeoLocationEnricher implements Enricher, ManyEnricher
{
    const VERSION = "1";

    const SERVICE_URL = 'http://ip-api.com/json/';

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
        $promises = [];


        foreach ($responses as $key => $response) {
            if ($response->getServerIP()) {
                $ip = $response->getServerIP();
            } else {
                $ip = gethostbyname($response->getRequestUri()->getHost());
            }
            $promises[$key] = $this->client->getAsync(self::SERVICE_URL . $ip, [
                RequestOptions::TIMEOUT => 1,
                RequestOptions::ALLOW_REDIRECTS => false
            ]);
        }

        $ipResponses = Utils::settle($promises)->wait();

        foreach ($ipResponses as $key => $ipResponse) {
            if ($ipResponse['state'] === 'fulfilled') {
                $body = (string)$ipResponse['value']->getBody();
                $responses[$key]->enrich(self::getIdentifier(), json_decode($body, true));
            }
        }
    }

    static public function getIdentifier(): string
    {
        return self::class . '_' . self::VERSION;
    }
}
