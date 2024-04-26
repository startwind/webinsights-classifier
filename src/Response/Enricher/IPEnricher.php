<?php

namespace Startwind\WebInsights\Response\Enricher;

use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Util\UrlHelper;

class IPEnricher implements Enricher
{
    const VERSION = "2";

    public const FIELD_IP = 'ip';

    public function enrich(HttpResponse $response): array|false
    {
        if (!$response->getServerIP()) {
            putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1');
            $domain = UrlHelper::getDomain($response->getRequestUri());
            return [
                self::FIELD_IP => gethostbyname($domain . '.')
            ];
        } else {
            return [
                self::FIELD_IP => $response->getServerIP()
            ];
        }
    }

    static public function getIdentifier(): string
    {
        return self::class . '_' . self::VERSION;
    }
}
