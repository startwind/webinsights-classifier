<?php

namespace Startwind\WebInsights\Classification\Feeder;

use Psr\Log\LoggerAwareTrait;
use Startwind\WebInsights\Classification\Domain\Domain;
use Startwind\WebInsights\Classification\Domain\DomainContainer;
use Startwind\WebInsights\Configuration\Exception\ConfigurationException;
use Startwind\WebInsights\Response\Retriever\LoggerAwareRetriever;
use Startwind\WebInsights\Util\OptionsHelper;

class FileFeeder implements Feeder, LoggerAwareRetriever
{
    use LoggerAwareTrait;

    private DomainContainer $domainContainer;
    private string $filename;

    public function __construct(array $options)
    {
        $this->domainContainer = new DomainContainer();

        OptionsHelper::assertValid($options, ['filename']);

        $filename = $options['filename'];

        if (!file_exists($filename)) {
            throw new ConfigurationException('The given file (' . $filename . ') does not exist.');
        }

        $this->filename = $filename;

        $classificationStrings = file($filename);

        foreach (array_unique($classificationStrings) as $domain) {
            if (\str_starts_with($domain, '#')) continue;

            try {
                $domainObject = new Domain($domain);
            } catch (\Exception $e) {
                // $this->logger->warning($e->getMessage());
                continue;
            }

            if ($domainObject->getDomain() === 'https://') continue;

            $this->domainContainer->addDomain($domainObject);
        }
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getDomainContainer(): DomainContainer
    {
        return $this->domainContainer;
    }
}
