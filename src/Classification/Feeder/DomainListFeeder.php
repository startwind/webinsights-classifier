<?php

namespace Startwind\WebInsights\Classification\Feeder;

class DomainListFeeder implements Feeder
{
    private array $domains = [];

    /**
     * @param array $domains
     */
    public function __construct(array $domains)
    {
        $this->domains = $domains;
    }

    public function getDomains(): array
    {
        return $this->domains;
    }
}
