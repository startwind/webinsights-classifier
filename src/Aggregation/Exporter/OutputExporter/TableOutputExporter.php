<?php

namespace Startwind\WebInsights\Aggregation\Exporter\OutputExporter;

use Startwind\WebInsights\Aggregation\Exporter\FinishExporter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class TableOutputExporter extends FinishExporter
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function finish(int $numberOfProcessedWebsites): void
    {
        foreach ($this->aggregationResults as $aggregationResult) {

            if (!$aggregationResult->hasResults()) continue;

            $this->output->writeln(['', '<comment>' . $aggregationResult->getGenerator() . '</comment>', '']);
            $table = new Table($this->output);

            if ($aggregationResult->hasMultipleResults()) {
                // @todo implement multiple results handling
            } else {
                $table->setHeaders(['Group', 'Count']);
                $rows = [];
                foreach ($aggregationResult->getResults() as $key => $value) {
                    $rows[] = [$key, $value];
                }
                $table->setRows($rows);
                $table->render();
            }
        }
    }
}
