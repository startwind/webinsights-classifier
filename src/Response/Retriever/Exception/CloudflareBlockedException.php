<?php

namespace Startwind\WebInsights\Response\Retriever\Exception;

class CloudflareBlockedException extends BlockedException
{
    public function __construct()
    {
        parent::__construct('Request was blocked by Cloudflare with a 403 status code.');
    }
}
