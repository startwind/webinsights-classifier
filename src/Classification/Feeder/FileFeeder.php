<?php

namespace Startwind\WebInsights\Classification\Feeder;

use Startwind\WebInsights\Configuration\Exception\ConfigurationException;

class FileFeeder implements Feeder
{
    private array $domains = [];
    private string $filename;

    public function __construct(string $filename)
    {
        if (!file_exists($filename)) {
            throw new ConfigurationException('The given file (' . $filename . ') does not exist.');
        }

        $this->filename = $filename;

        $classificationStrings = file($filename);

        $this->domains = array_unique($classificationStrings);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getDomains(): array
    {
        return $this->domains;
    }
}
