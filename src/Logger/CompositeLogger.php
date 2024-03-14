<?php

namespace Startwind\WebInsights\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Symfony\Component\Console\Output\OutputInterface;

class CompositeLogger implements LoggerInterface, OutputAwareLogger
{
    use LoggerTrait;

    /**
     * @var \Psr\Log\LoggerInterface[]
     */
    private array $loggers = [];

    public function addLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }

    public function Log($level, \Stringable|string $message, array $context = []): void
    {
        foreach ($this->loggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }

    public function setOutput(OutputInterface $output): void
    {
        foreach ($this->loggers as $logger) {
            if ($logger instanceof OutputAwareLogger) {
                $logger->setOutput($output);
            }
        }
    }
}
