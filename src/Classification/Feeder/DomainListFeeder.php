<?php

namespace Startwind\WebInsights\Classification\Feeder;

use Startwind\WebInsights\Classification\Domain\Domain;
use Startwind\WebInsights\Classification\Domain\DomainContainer;

class DomainListFeeder implements Feeder
{
    private DomainContainer $domainContainer;

    public function __construct(array $domains)
    {
        $this->domainContainer = new DomainContainer();

        foreach ($domains as $domain) {
            $this->domainContainer->addDomain(new Domain($domain));
        }
    }

    public function getDomainContainer(): DomainContainer
    {
        return $this->domainContainer;
    }
}
