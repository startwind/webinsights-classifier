<?php

namespace Startwind\WebInsights\Hosting\IpRange;

use Psr\Log\LoggerAwareTrait;

class CompositeIpRangeExtractor implements IpRangeExtractor
{
    use LoggerAwareTrait;

    /**
     * @var IpRangeExtractor[]
     */
    private array $extractors = [];

    public function addExtractor(IpRangeExtractor $asExtractor): void
    {
        $this->extractors[] = $asExtractor;
    }

    public function getIpRange(string $as): array
    {
        foreach ($this->extractors as $ipRangeExtractor) {
            try {
                $as = $ipRangeExtractor->getIpRange($as);
                return $as;
            } catch (\Exception $exception) {
                $this->logger->warning('Unable to use ' . get_class($ipRangeExtractor) . ': ' . $exception->getMessage());
            }
        }

        throw new \RuntimeException('Unable to get as. No extractor returned an answer.');
    }
}
