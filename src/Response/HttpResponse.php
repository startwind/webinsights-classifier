<?php

namespace Startwind\WebInsights\Response;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class HttpResponse implements \JsonSerializable
{
    private string $body;

    private array $headers;
    private array $lowerCaseHeaders = [];

    private int $statusCode;

    private array $enrichmentData = [];

    private UriInterface $requestUri;

    private int $transferTimeInMs;
    private string $serverIP;
    private array $certInfo;
    private ?UriInterface $effectiveUri = null;

    private HtmlDocument $htmlDocument;

    public function __construct(string $body, array $headers, int $statusCode, UriInterface $requestUri, int $transferTimeInMs, string $serverIp, array $certInfo = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;

        $this->requestUri = $requestUri;

        $this->headers = $headers;

        $this->serverIP = $serverIp;

        $this->transferTimeInMs = $transferTimeInMs;

        $this->certInfo = $certInfo;

        $this->htmlDocument = new HtmlDocument($this->body);

        foreach ($this->headers as $key => $value) {
            $this->lowerCaseHeaders[strtolower($key)] = $value;
        }
    }

    public function getEffectiveUri(): UriInterface
    {
        return $this->effectiveUri;
    }

    public function hasEffectiveUri(): bool
    {
        return !is_null($this->effectiveUri);
    }

    public function setEffectiveUri(UriInterface $uri): void
    {
        $this->effectiveUri = $uri;
    }

    public function getTransferTimeInMs(): int
    {
        return $this->transferTimeInMs;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders($lowerCase = false): array
    {
        if ($lowerCase) {
            return $this->lowerCaseHeaders;
        } else {
            return $this->headers;
        }
    }

    public function headerContains(string $headerName, string $containedString, bool $caseSensitive = false): bool
    {
        if (!$this->hasHeader($headerName, $caseSensitive)) return false;

        $header = $this->getHeader($headerName, $caseSensitive);

        if (!$caseSensitive) {
            $containedString = strtolower($containedString);
        }

        foreach ($header as $value) {
            if ($caseSensitive) {
                if (str_contains($value, $containedString)) return true;
            } else {
                if (str_contains(strtolower($value), $containedString)) return true;
            }
        }

        return false;
    }

    public function getHtmlDocument(): HtmlDocument
    {
        return $this->htmlDocument;
    }

    public function hasHeader(string $headerName, bool $caseSensitive = false): bool
    {
        if ($caseSensitive) {
            return array_key_exists($headerName, $this->headers);
        } else {
            return array_key_exists(strtolower($headerName), $this->lowerCaseHeaders);
        }
    }

    public function getHeader(string $headerName, bool $caseSensitive = false): array
    {
        if ($caseSensitive) {
            return $this->headers[$headerName];
        } else {
            return $this->lowerCaseHeaders[strtolower($headerName)];
        }
    }

    public function getRequestUri(): UriInterface
    {
        return $this->requestUri;
    }

    public function enrich(string $enricherName, array $data): void
    {
        $this->enrichmentData[$enricherName] = $data;
    }

    public function hasEnrichment(string $enricherName): bool
    {
        return array_key_exists($enricherName, $this->enrichmentData);
    }

    public function getEnrichmentNames(): array
    {
        return array_keys($this->enrichmentData);
    }

    public function getEnrichment(string $enricherName): array
    {
        return $this->enrichmentData[$enricherName];
    }

    public function setEnrichmentData(array $data): void
    {
        $this->enrichmentData = $data;
    }

    public function getServerIP(): string
    {
        return $this->serverIP;
    }

    public function jsonSerialize(): array
    {
        return [
            'uri' => (string)$this->requestUri,
            'headers' => $this->headers,
            'body' => mb_convert_encoding($this->body, 'UTF-8'),
            'statusCode' => $this->statusCode,
            'transferTimeInMs' => $this->transferTimeInMs,
            'enrichmentData' => $this->enrichmentData,
            'serverIP' => $this->serverIP,
            'effectiveUri' => (string)$this->effectiveUri
        ];
    }

    public function getSSLCertificateInfo(): array
    {
        return $this->certInfo;
    }

    public function hasSSLCertificateInfo(): bool
    {
        return count($this->certInfo) > 0;
    }

    public static function fromArray(array $array): self
    {
        if (array_key_exists('transferTimeInSeconds', $array)) {
            $array['transferTimeInMs'] = $array['transferTimeInSeconds'];
        }

        if (!array_key_exists('serverIP', $array)) {
            $array['serverIP'] = '';
        }

        $response = new self(
            $array['body'],
            $array['headers'],
            $array['statusCode'],
            new Uri($array['uri']),
            $array['transferTimeInMs'],
            $array['serverIP'],
        );

        if (array_key_exists('enrichmentData', $array)) {
            $response->setEnrichmentData($array['enrichmentData']);
        }

        if (array_key_exists('effectiveUri', $array)) {
            $response->setEffectiveUri(new Uri($array['effectiveUri']));
        }

        return $response;
    }
}
