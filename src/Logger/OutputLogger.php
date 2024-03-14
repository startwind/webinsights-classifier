<?php

namespace Startwind\WebInsights\Logger;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

class OutputLogger extends Logger implements OutputAwareLogger
{
    private OutputInterface $output;

    public function __construct(string $logLevel = LogLevel::ALERT, array $option = [])
    {
        $this->logLevel = $logLevel;
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    protected function doLog($level, \Stringable|string $message, array $context = []): void
    {
        if (self::LEVELS[$level] >= self::LEVELS[LogLevel::ALERT]) {
            $this->output->writeln(date('Y-m-d H:i:s') . ' - ' . $level . ' - <error>' . $message . '</error>');
        } else {
            $this->output->writeln(date('Y-m-d H:i:s') . ' - ' . $level . ' - <comment>' . $message . '</comment>');
        }
    }
}
