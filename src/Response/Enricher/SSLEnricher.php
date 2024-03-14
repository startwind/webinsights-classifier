<?php

namespace Startwind\WebInsights\Response\Enricher;

use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Util\UrlHelper;

class SSLEnricher implements Enricher
{
    const VERSION = "2";

    public function enrich(HttpResponse $response): array|false
    {
        $domain = UrlHelper::getDomain($response->getRequestUri());
        return $this->getInformation($domain);
    }

    private function getInformation(string $domain): array
    {
        $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));

        $read = @stream_socket_client(
            "ssl://" . $domain . ":443",
            $errno,
            $errstr,
            1,
            STREAM_CLIENT_CONNECT,
            $get
        );

        if ($read === false) {
            return ["failure" => 'Unable to open socket for ' . $domain . '.'];
        }

        $cert = stream_context_get_params($read);
        $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);

        if ($certinfo === false) {
            return ["failure" => 'Unable to parse SSL information for ' . $domain . '.'];
        }

        return $certinfo;
    }

    static public function getIdentifier(): string
    {
        return self::class . '_' . self::VERSION;
    }
}
