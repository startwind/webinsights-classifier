<?php

namespace Startwind\WebInsights\Response\Enricher;

use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Util\UrlHelper;
use Symfony\Component\Process\Process;

class IPEnricher implements Enricher, ManyEnricher
{
    const VERSION = "2";

    public const FIELD_IP = 'ip';

    public function enrich(HttpResponse $response): array|false
    {
        $this->enrichMany([$response]);
        if ($response->hasEnrichment(self::getIdentifier())) {
            return $response->getEnrichment(self::getIdentifier());
        } else {
            return false;
        }
    }

    public function enrichMany(array $responses): void
    {
        /** @var Process[] $processes */
        $processes = [];

        foreach ($responses as $key => $response) {
            $domain = UrlHelper::getDomain($response->getRequestUri());
            $process = Process::fromShellCommandline('dig +short ' . $domain);
            $process->start();
            $processes[$key] = $process;
        }

        foreach ($processes as $key => $process) {
            $process->wait();
            $output = $process->getOutput();

            $ip = trim($output);

            if(str_contains( $ip, "\n",)) {
                $ips = explode("\n", $ip);
                $ip = $ips[0];
            }

            $responses[$key]->enrich(self::getIdentifier(), [self::FIELD_IP => $ip]);
        }
    }

    static public function getIdentifier(): string
    {
        return self::class . '_' . self::VERSION;
    }
}
