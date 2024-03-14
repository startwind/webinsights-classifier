<?php

namespace Startwind\WebInsights\Response\Retriever;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

interface LoggerAwareRetriever extends LoggerAwareInterface
{
    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger): void;
}
