<?php

namespace Startwind\WebInsights\Storage;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Response\HttpResponse;

class NullStorage implements Storage
{
    public function __construct(array $options = [])
    {
    }

    public function setHttpResponse(UriInterface $uri, HttpResponse $response): void
    {

    }

    public function getHttpResponse(UriInterface $uri): HttpResponse|false
    {
        return false;
    }

    public function finish(): void
    {

    }
}
