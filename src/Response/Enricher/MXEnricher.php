<?php

namespace Startwind\WebInsights\Response\Enricher;

use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Util\UrlHelper;
use Symfony\Component\Process\Process;

class MXEnricher implements Enricher, ManyEnricher
{
    const VERSION = "1";

    public const FIELD_RECORDS = 'records';

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
            $process = Process::fromShellCommandline('dig +short MX ' . $domain);
            $process->start();
            $processes[$key] = $process;
        }

        foreach ($processes as $key => $process) {
            $process->wait();
            $output = $process->getOutput();

            $mxEntries = explode("\n", trim($output));

            $records = [];

            foreach ($mxEntries as $mxEntry) {
                if ($mxEntry) {
                    $rawRecords = explode(" ", $mxEntry);
                    if (array_key_exists(1, $rawRecords)) {
                        $records[] = (rtrim(trim($rawRecords[1]), '.'));
                    } else {
                        $records[] = (rtrim(trim($rawRecords[0]), '.'));
                    }
                }
            }

            $responses[$key]->enrich(self::getIdentifier(), [self::FIELD_RECORDS => $records]);
        }
    }

    static public function getIdentifier(): string
    {
        return self::class . '_' . self::VERSION;
    }
}
