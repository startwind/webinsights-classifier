<?php

namespace Startwind\WebInsights\Classification\Exporter;

use Startwind\WebInsights\Classification\ClassificationResult;

interface Exporter
{
    /**
     * Handle the classification result (tags, uri) of a classification.
     */
    public function export(ClassificationResult $classificationResult): void;

    /**
     * This function is called after before the application return.
     *
     * This can be used to write aggregated data to a file or the database.
     */
    public function finish(): string;
}
