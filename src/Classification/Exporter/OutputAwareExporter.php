<?php

namespace Startwind\WebInsights\Classification\Exporter;

use Symfony\Component\Console\Output\OutputInterface;

interface OutputAwareExporter
{
    /**
     * Set the Symfony output object used to write to the CLI.
     */
    public function setOutput(OutputInterface $output): void;
}
