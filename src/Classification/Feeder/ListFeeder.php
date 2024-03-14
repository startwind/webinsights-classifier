<?php

namespace Startwind\WebInsights\Classification\Feeder;

class ListFeeder implements Feeder
{
    private array $domains = [];

    /**
     * @param array $domains
     */
    public function __construct(array $option = [])
    {
        $this->domains = $option['domains'];
    }

    public function getDomains(): array
    {
        return $this->domains;
    }
}
