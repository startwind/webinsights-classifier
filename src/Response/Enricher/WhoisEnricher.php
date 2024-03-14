<?php

namespace Startwind\WebInsights\Response\Enricher;

use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Util\WhoisHelper;

class WhoisEnricher implements Enricher
{
    const VERSION = "1";

    const FIELD_REGISTRAR = 'registrar';

    public function enrich(HttpResponse $response): array|false
    {
        try {
            $registrar = WhoisHelper::queryRegistrar($response->getRequestUri());
        } catch (\Exception $exception) {
            return false;
        }

        if ($registrar) {
            return [self::FIELD_REGISTRAR => $registrar];
        } else {
            return false;
        }
    }

    static public function getIdentifier(): string
    {
        return self::class . '_' . self::VERSION;
    }
}
