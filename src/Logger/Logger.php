<?php

namespace Startwind\WebInsights\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

abstract class Logger implements LoggerInterface
{
    use LoggerTrait;

    protected string $logLevel = LogLevel::ALERT;

    const LEVELS = [
        LogLevel::EMERGENCY => 800,
        LogLevel::ALERT => 700,
        LogLevel::CRITICAL => 600,
        LogLevel::ERROR => 500,
        LogLevel::WARNING => 400,
        LogLevel::NOTICE => 300,
        LogLevel::INFO => 200,
        LogLevel::DEBUG => 100
    ];

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if (self::LEVELS[$this->logLevel] <= self::LEVELS[$level]) {
            $this->doLog($level, $message, $context);
        }
    }

    abstract protected function doLog($level, \Stringable|string $message, array $context = []): void;
}
