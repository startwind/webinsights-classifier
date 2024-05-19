<?php

namespace Startwind\WebInsights\Response\Retriever;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Startwind\WebInsights\Response\Enricher\Enricher;
use Startwind\WebInsights\Response\Enricher\Exception\EnrichmentFailedException;
use Startwind\WebInsights\Response\Enricher\ManyEnricher;
use Startwind\WebInsights\Response\HttpResponse;
use Startwind\WebInsights\Response\Retriever\Exception\CloudflareBlockedException;
use Startwind\WebInsights\Storage\NullStorage;
use Startwind\WebInsights\Storage\Storage;
use Startwind\WebInsights\Util\Timer;

class GuzzleRetriever implements Retriever, LoggerAwareRetriever, HttpClientAwareRetriever, StorageAwareRetriever, EnrichmentAwareRetriever
{
    private const THRESHOLD_ENRICHER_TIME_MAX = 300;

    private const DEFAULT_PARALLEL_REQUESTS = 5;

    /**
     * @var \Psr\Http\Message\UriInterface[]
     */
    private array $uris;

    private LoggerInterface $logger;

    private int $parallelRequests;

    private int $position = 0;

    private int $limit = self::LIMIT_UNLIMITED;

    private Client $client;

    private array $clientOptions = [];

    /**
     * @var \Startwind\WebInsights\Response\Enricher\Enricher[]
     */
    private array $enrichers = [];

    /**
     * @var HttpResponse[]
     */
    private array $preparedHttpResponses = [];

    private array $defaultOptions = [
        'parallelRequests' => self::DEFAULT_PARALLEL_REQUESTS,
    ];

    private Storage $storage;

    public function __construct(array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);

        $this->parallelRequests = $options['parallelRequests'];

        $this->logger = new NullLogger();
        $this->storage = new NullStorage();

        if (array_key_exists('clientOptions', $options)) {
            $this->clientOptions = $options['clientOptions'];
        }
    }

    /**
     * @inheritDoc
     */
    public function setHttpClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function setUris(array $uris): void
    {
        $this->uris = $uris;

        if ($this->limit === self::LIMIT_UNLIMITED) {
            $this->limit = count($uris);
        }
    }

    /**
     * @inheritDoc
     */
    public function next(): HttpResponse|false
    {
        if ($this->position > count($this->uris)) {
            $this->logger->info('Retriever finished. Position at the end of file.');
            return false;
        }
        if ($this->position > $this->limit + 1) {
            $this->logger->info('Retriever finished. Position greater than limit.');
            return false;
        }

        /** @var HttpResponse $nextResponse */
        $nextResponse = array_shift($this->preparedHttpResponses);

        $this->position++;

        if (!$nextResponse) {
            $this->logger->info('Prepared document list empty. Fetching new ones.');
            $this->prepareHttpResponses();
            return $this->next();
        } else {
            $this->logger->info('Response returned ("' . $nextResponse->getRequestUri() . '")');
            return $nextResponse;
        }
    }

    private function prepareHttpResponses(): void
    {
        $requests = [];
        $responseStats = [];

        $options = $this->clientOptions;

        $options['curl'] = [CURLOPT_CERTINFO => true];

        $options['allow_redirects'] = [
            'track_redirects' => true
        ];

        // $options['headers']['User-Agent'] = 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

        $certInfos = [];

        $options['on_stats'] = function (TransferStats $stats) use (&$responseStats, &$certInfos) {

            $handlerStats = $stats->getHandlerStats();

            $host = $stats->getRequest()->getUri()->getHost();

            if (!array_key_exists($host, $certInfos)) {
                if (count($handlerStats['certinfo']) > 0) {
                    $parts = explode(', ', $handlerStats['certinfo'][0]['Issuer']);

                    foreach ($parts as $part) {
                        $elements = explode(' = ', $part);
                        if (array_key_exists(1, $elements)) {
                            $certInfos[$host][$elements[0]] = $elements[1];
                        }
                    }
                }
            }

            $responseStats[(string)($stats->getRequest()->getUri())]['transferTime'] = $stats->getTransferTime() * 1000;
            $responseStats[(string)($stats->getRequest()->getUri())]['effectiveUrl'] = $stats->getEffectiveUri();
            $responseStats[(string)($stats->getRequest()->getUri())]['ip'] = $stats->getHandlerStats()['primary_ip'];

            if (array_key_exists($host, $certInfos)) {
                $responseStats[(string)($stats->getRequest()->getUri())]['cert']['issuer'] = $certInfos[$host];
            } else {
                $responseStats[(string)($stats->getRequest()->getUri())]['cert'] = [];
            }
        };

        // $this->parallelRequests = 60;

        for ($i = 0; $i < $this->parallelRequests; $i++) {

            $position = $this->position + $i - 1;

            if ($position > $this->limit + 1) continue;
            if ($position >= count($this->uris)) continue;

            $uri = $this->uris[$position];

            if ($storedResponse = $this->storage->getHttpResponse($uri)) {
                $storeNeeded = $this->enrich($storedResponse);
                if ($storeNeeded) {
                    $this->storage->setHttpResponse($uri, $storedResponse);
                }

                $this->preparedHttpResponses[] = $storedResponse;

            } else {
                if (array_key_exists($position, $this->uris)) {
                    $requests[(string)$this->uris[$position]] = $this->client->getAsync($this->uris[$position], $options);
                }
            }
        }

        $this->logger->debug(count($this->preparedHttpResponses) . ' HttpResponse(s) loaded from cache.');

        $this->logger->info('Running ' . count($requests) . ' async Guzzle request(s).');

        $responses = Utils::settle($requests)->wait();

        $rawResponses = [];

        foreach ($responses as $uriString => $promiseResponse) {
            try {
                $response = $this->convertToResponse($promiseResponse);
            } catch (\RuntimeException $exception) {
                $this->logger->alert('Unable to retrieve response for ' . $uriString . '. ' . $exception->getMessage());
                continue;
            }

            $requestUri = new Uri($uriString);

            $httpResponse = $this->createHttpResponse(
                $response,
                $requestUri,
                (int)$responseStats[(string)$requestUri]['transferTime'],
                (string)$responseStats[(string)$requestUri]['ip'],
                $responseStats[(string)$requestUri]['cert'],
            );

            if ($response->hasHeader('X-Guzzle-Redirect-History')) {
                $httpResponse->setEffectiveUri(new Uri($response->getHeader('X-Guzzle-Redirect-History')[0]));
            }

            $rawResponses[$uriString] = $httpResponse;

            $this->storage->setHttpResponse($requestUri, $httpResponse);
        }

        $enrichedResponses = $this->enrichMany($rawResponses);

        foreach ($enrichedResponses as $uriString => $enrichedResponse) {
            $this->preparedHttpResponses[] = $enrichedResponse;
            $this->storage->setHttpResponse(new Uri($uriString), $enrichedResponse);
        }

        $this->logger->info('Prepared ' . count($this->preparedHttpResponses) . ' HttpResponse(s).');
    }

    private function enrich(HttpResponse $httpResponse): bool
    {
        $storeNeeded = false;
        foreach ($this->enrichers as $enricher) {
            if (!$httpResponse->hasEnrichment($enricher::getIdentifier())) {
                try {
                    $storeNeeded = $this->enrichSingle($enricher, $httpResponse) || $storeNeeded;
                } catch (\Exception $exception) {
                    $this->logger->alert('Enrichment failed for ' . get_class($enricher) . '. ' . $exception->getMessage());
                    continue;
                }
            }
        }

        return $storeNeeded;
    }

    private function enrichMany(array $httpResponses): array
    {
        $enrichedResponses = $httpResponses;

        $timer = new Timer();

        foreach ($this->enrichers as $enricher) {
            if ($enricher instanceof ManyEnricher) {
                try {
                    $timer->start();
                    $enricher->enrichMany($enrichedResponses);
                    $time = $timer->getTimePassed();
                    if ($time > 1000) {
                        $this->logger->alert('Multi enrichment took too long for ' . get_class($enricher) . ' (' . $time . ').');
                    }
                } catch (\Exception $exception) {
                    $this->logger->alert('Enrichment failed for ' . get_class($enricher) . '. ' . $exception->getMessage());
                }
            } else {
                foreach ($enrichedResponses as $enrichedResponse) {
                    try {
                        $timer->start();
                        $this->enrichSingle($enricher, $enrichedResponse);
                        $time = $timer->getTimePassed();
                        if ($time > 1000) {
                            $this->logger->alert('Single enrichment took too long for ' . get_class($enricher) . ' (' . $time . ').');
                        }
                    } catch (\Exception $exception) {
                        $this->logger->alert('Enrichment failed for ' . get_class($enricher) . '. ' . $exception->getMessage());
                    }
                }
            }
        }

        return $enrichedResponses;
    }

    private function enrichSingle(Enricher $enricher, HttpResponse $httpResponse): bool
    {
        $storeNeeded = false;
        $timer = new Timer();
        try {
            if ($data = $enricher->enrich($httpResponse)) {
                $httpResponse->enrich($enricher::getIdentifier(), $data);
                $storeNeeded = true;
            } else {
                $this->logger->alert('No enrichment data received from ' . get_class($enricher));
            }
        } catch (EnrichmentFailedException $exception) {
            $this->logger->alert('Enrichment failed for ' . get_class($enricher) . '. ' . $exception->getMessage());
            throw $exception;
        }

        $time = $timer->getTimePassed();

        if ($time > self::THRESHOLD_ENRICHER_TIME_MAX) {
            $this->logger->alert('Enrichment slow for ' . get_class($enricher) . '. The enrichment took ' . $time . ' ms.');
        }
        return $storeNeeded;
    }

    private function convertToResponse(array $promiseResponse): Response
    {
        if ($promiseResponse['state'] === 'fulfilled') {
            $response = $promiseResponse['value'];
        } else if ($promiseResponse['state'] === 'rejected') {
            $reason = $promiseResponse['reason'];
            if ($reason instanceof ClientException) {
                $response = $reason->getResponse();
                if ($response->getStatusCode() === 403 && $response->hasHeader('Server') && $response->getHeader('Server')[0] === 'cloudflare') {
                    throw new CloudflareBlockedException();
                }
            } elseif ($reason instanceof ConnectException) {
                $response = new Response(599, [], '<html></html>');
            } else {
                if ($promiseResponse['reason'] instanceof \Exception) {
                    $message = str_replace(PHP_EOL, '', substr($promiseResponse['reason']->getMessage(), 0, 200));
                    $this->logger->alert('Unknown exception thrown while preparing responses. Exception class: ' . get_class($promiseResponse['reason']) . ', message: ' . $message);
                } else {
                    $this->logger->alert('Unknown error thrown while preparing responses. Reason class: ' . get_class($promiseResponse['reason']));
                }
                $response = new Response(599, [], '<html></html>');
            }
        } else {
            throw $promiseResponse['reason'];
        }

        return $response;
    }

    private function createHttpResponse(Response $response, UriInterface $requestUri, int $transferTime, string $ip, array $certificateInfo = []): HttpResponse
    {
        // This little workaround is needed as Guzzle cannot handle streams that
        // do not end.

        $body = $response->getBody();
        $buffer = '';

        while (!$body->eof() && (strlen($buffer) < 52428800)) {
            $buffer .= $body->read(1024);
        }

        if (!$body->eof()) {
            $this->logger->alert('Body size limit reached. Did not finish loading body of ' . $requestUri . '.');
        }

        $body->close();

        return new HttpResponse(
            $buffer,
            $response->getHeaders(),
            $response->getStatusCode(),
            $requestUri,
            $transferTime,
            $ip,
            $certificateInfo
        );
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        $this->logger->info('Added logger "' . get_class($logger) . '" to GuzzleRetriever.');
    }

    /**
     * @inheritDoc
     */
    public function setStorage(Storage $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return $this->position - 1;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }


    public function addEnricher(Enricher $enricher): void
    {
        $this->enrichers[] = $enricher;
    }
}
