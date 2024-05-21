<?php

namespace Startwind\WebInsights\Util;

class Timer
{
    public const UNIT_MICROSECONDS = 'mis';
    public const UNIT_MILLISECONDS = 'ms';
    public const UNIT_SECONDS = 's';

    private float $startTime;

    public function __construct()
    {
        $this->start();
    }

    public function start(): void
    {
        $this->startTime = floor(microtime(true) * 1000 * 1000);
    }

    public function getTimePassed(string $unit = self::UNIT_MILLISECONDS): int
    {
        $now = floor(microtime(true) * 1000 * 1000);
        $time = $now - $this->startTime;

        return match ($unit) {
            self::UNIT_MICROSECONDS => $time,
            self::UNIT_MILLISECONDS => floor($time / 1000),
            self::UNIT_SECONDS => (int)($time / 1000 / 1000),
            default => throw new \BadMethodCallException('The given unit "' . $unit . '" is unknown. '),
        };
    }
}
