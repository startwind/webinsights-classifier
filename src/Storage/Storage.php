<?php

namespace Startwind\WebInsights\Storage;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Configuration\Initializable;
use Startwind\WebInsights\Response\HttpResponse;

interface Storage extends Initializable
{
    public function setHttpResponse(UriInterface $uri, HttpResponse $response);

    public function getHttpResponse(UriInterface $uri);

    public function finish(): void;
}
