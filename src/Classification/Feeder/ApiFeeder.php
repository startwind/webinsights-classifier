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

    public function __construct(array $options)
    {
        $this->domainContainer = new DomainContainer();

        $apiEndpoint = $options['apiEndpoint'];

        $client = new Client();

        $response = $client->post($apiEndpoint, [
            RequestOptions::JSON => [
                'tags' => $options['tags']
            ]
        ]);

        $result = json_decode($response->getBody(), true);

        $domains = $result['data']['domains'];

        foreach ($domains as $domain) {
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
