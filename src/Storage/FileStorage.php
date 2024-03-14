<?php

namespace Startwind\WebInsights\Storage;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Response\HttpResponse;

class FileStorage implements Storage
{
    private string $directory;

    private int $expiresAfter;

    private array $defaultOptions = [
        'directory' => 'cache',
        'expiresAfter' => 60 * 60 * 24 * 30 /* month */
    ];

    private FilesystemCachePool $cacheItemPool;

    public function __construct(array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);

        $this->directory = $options['directory'];

        $this->expiresAfter = $options['expiresAfter'];

        $filesystemAdapter = new Local($this->directory);
        $filesystem = new Filesystem($filesystemAdapter);
        $this->cacheItemPool = new FilesystemCachePool($filesystem);
    }

    public function setHttpResponse(UriInterface $uri, HttpResponse $response): void
    {
        $item = $this->cacheItemPool->getItem($this->getCacheKey($uri));

        $item->set($response->jsonSerialize());

        $item->expiresAfter($this->expiresAfter);

        $this->cacheItemPool->save($item);
    }

    public function getHttpResponse(UriInterface $uri): HttpResponse|false
    {
        $item = $this->cacheItemPool->getItem($this->getCacheKey($uri));

        if ($item->isHit()) {
            return HttpResponse::fromArray($item->get());
        } else {
            return false;
        }
    }

    private function getCacheKey(UriInterface $uri): string
    {
        return md5((string)$uri);
    }
    public function finish(): void
    {

    }
}
