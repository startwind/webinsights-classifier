<?php

namespace Startwind\WebInsights\Classification\Domain;

use GuzzleHttp\Psr7\Uri;

class DomainContainer
{
    /** @var Domain[] */
    private array $domains = [];

    public function addDomain(Domain $domain): void
    {
        $this->domains[$domain->getDomain()] = $domain;
    }

    public function getDomain(string $domainString): Domain
    {
        return $this->domains[$domainString];
    }

    /**
     * @return Domain[]
     */
    public function getDomains(): array
    {
        return $this->domains;
    }

    /**
     * @return Uri[]
     */
    public function toUriList(): array
    {
        $uris = [];
        foreach ($this->domains as $domain) {
            $uris[] = $domain->getUri();
        }
        return $uris;
    }
}