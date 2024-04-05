<?php

namespace Startwind\WebInsights\Classification\Feeder;

use Startwind\WebInsights\Classification\Domain\Domain;
use Startwind\WebInsights\Classification\Domain\DomainContainer;

class ListFeeder implements Feeder
{
    private DomainContainer $domainContainer;

    public function __construct(array $option = [])
    {
        $this->domainContainer = new DomainContainer();

        foreach ($option['domains'] as $domain) {
            if (is_array($domain)) {
                $this->domainContainer->addDomain(new Domain($domain['domain'], $domain['tags']));
            } else {
                $this->domainContainer->addDomain(new Domain($domain));
            }
        }
    }

    public function getDomainContainer(): DomainContainer
    {
        return $this->domainContainer;
    }
}
