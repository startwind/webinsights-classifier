<?php

namespace Startwind\WebInsights\Storage;

interface RunIdAwareStorage
{
    public function setRunId(string $runId): void;
}
