<?php

namespace Startwind\WebInsights\Configuration;

class Resume implements \JsonSerializable
{
    public const FILE_PREFIX = 'resume-';

    private string $runId;

    private array $config;
    private string $inputFile;

    private int $position = 0;

    public function __construct(string $runId, array $config, string $inputFile)
    {
        $this->runId = $runId;
        $this->inputFile = $inputFile;
        $this->config = $config;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function jsonSerialize(): array
    {
        return [
            'runId' => $this->runId,
            'config' => $this->config,
            'inputFile' => $this->inputFile,
            'position' => $this->position
        ];
    }
}
