<?php

namespace Startwind\WebInsights\Hosting\AS;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class CompositeAsExtractor implements AsExtractor, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var AsExtractor[]
     */
    private array $extractors = [];

    public function addExtractor(AsExtractor $asExtractor): void
    {
        $this->extractors[] = $asExtractor;
    }

    public function getAs(string $domain): string
    {
        foreach ($this->extractors as $asExtractor) {
            try {
                $as = $asExtractor->getAs($domain);
                return $as;
            } catch (\Exception $exception) {
                $this->logger->warning('Unable to use ' . get_class($asExtractor) . ': ' . $exception->getMessage());
            }
        }

        throw new \RuntimeException('Unable to get as. No extractor returned an answer.');
    }

}
