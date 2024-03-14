<?php

namespace Startwind\WebInsights\Logger;

use Symfony\Component\Console\Output\OutputInterface;

interface OutputAwareLogger
{
    public function setOutput(OutputInterface $output): void;
}
