<?php

namespace Startwind\WebInsights\Classification\Feeder;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerAwareTrait;
use Startwind\WebInsights\Classification\Domain\Domain;
use Startwind\WebInsights\Classification\Domain\DomainContainer;
use Startwind\WebInsights\Response\Retriever\LoggerAwareRetriever;

class ApiFeeder implements Feeder, LoggerAwareRetriever
{
    use LoggerAwareTrait;

    private DomainContainer $domainContainer;
    private string $filename;

    private int $startWith = 0;

    public function __construct(array $options)
    {
        $this->domainContainer = new DomainContainer();

        $apiEndpoint = $options['apiEndpoint'];

        if (array_key_exists('startWith', $options)) {
            $this->startWith = $options['startWith'];
        }

        $client = new Client();

        $response = $client->post($apiEndpoint, [
            RequestOptions::JSON => [
                'tags' => $options['tags']
            ]
        ]);

        $result = json_decode($response->getBody(), true);

        $domains = $result['data']['domains'];

        $count = 0;

        foreach ($domains as $domain) {
            $count++;

            if ($count < $this->startWith) {
                continue;
            }

            if (\str_starts_with($domain, '#')) continue;

            try {
                $domainObject = new Domain($domain);
            } catch (\Exception) {
                continue;
            }

            if ($domainObject->getDomain() === 'https://') continue;

            $this->domainContainer->addDomain($domainObject);
        }
    }

    public function getDomainContainer(): DomainContainer
    {
        return $this->domainContainer;
    }
}
