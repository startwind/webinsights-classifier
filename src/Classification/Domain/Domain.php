<?php

namespace Startwind\WebInsights\Classification\Domain;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Util\UrlHelper;

class Domain
{
    private string $domain;

    /**
     * @var string[]
     */
    private array $tags;

    public function __construct(string $domain, array $tags = [])
    {
        $this->domain = $this->repairDomainString($domain);

        if (!filter_var($this->domain, FILTER_VALIDATE_URL)) {
            throw new \RuntimeException('The given domain "' . trim($domain) . '" is not valid.');
        }

        $this->tags = $tags;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getUri(): UriInterface
    {
        return new Uri($this->getDomain());
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    private function repairDomainString(string $string): string
    {
        $string = trim($string);

        if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
            return $string;
        }

        if (!\str_starts_with($string, 'http')) {
            $string = 'https://' . $string;
        }

        return (string)UrlHelper::rootUri(new Uri($string));
    }
}
