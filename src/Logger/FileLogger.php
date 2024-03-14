<?php

namespace Startwind\WebInsights\Logger;

class FileLogger extends Logger
{
    private $handle;

    public function __construct(string $logLevel, string $filename)
    {
        $this->logLevel = $logLevel;

        $dir = dirname($filename);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->handle = fopen($filename, 'a');
    }

    public function doLog($level, \Stringable|string $message, array $context = []): void
    {
        $data = date('Y-m-d H:i:s') . ' - ' . $level . ' - ' . $message . PHP_EOL;
        fwrite($this->handle, $data);
    }
}
