<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class WebServerClassifier extends HttpClassifier
{
    public const TAG_PREFIX = 'tech:webserver:';

    protected function doHttpClassification(HttpResponse $response): array
    {
        if ($response->headerContains('server', 'apache')) {
            return [self::TAG_PREFIX . 'apache'];
        }

        if ($response->headerContains('server', 'nginx')) {
            return [self::TAG_PREFIX . 'nginx'];
        }

        if ($response->headerContains('server', 'litespeed')) {
            return [self::TAG_PREFIX . 'litespeed'];
        }

        if ($response->headerContains('X-Turbo-Charged-By', 'litespeed')) {
            return [self::TAG_PREFIX . 'litespeed'];
        }

        if ($response->headerContains('server', 'AmazonS3')) {
            return [self::TAG_PREFIX . 'amazon-s3'];
        }

        if ($response->headerContains('server', 'iis')) {
            return [self::TAG_PREFIX . 'microsoft-iis'];
        }

        if ($response->headerContains('server', 'envoy')) {
            return [self::TAG_PREFIX . 'envoy'];
        }

        if ($response->headerContains('server', 'netlify')) {
            return [self::TAG_PREFIX . 'netlify'];
        }

        if ($response->headerContains('server', 'caddy')) {
            return [self::TAG_PREFIX . 'caddy'];
        }

        if ($response->headerContains('X-Powered-By', 'express')) {
            return [self::TAG_PREFIX . 'express-js'];
        }

        return [];
    }
}
