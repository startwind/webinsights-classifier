<?php

namespace Startwind\WebInsights\Classification\Exporter\Analytics;

use Startwind\WebInsights\Classification\Exporter\OutputAwareExporter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class CliAnalyticsExporter extends AnalyticsExporter implements OutputAwareExporter
{
    private int $barWidth;

    private bool $sortByNumber;

    private bool $hideSingleFindings;

    private array $defaultOptions = [
        'sortByNumber' => true,
        'barWidth' => 60,
        'hideSingleFindings' => false
    ];

    private OutputInterface $output;

    public function __construct(string $runId, array $options)
    {
        $options = array_merge($this->defaultOptions, $options);

        $this->sortByNumber = $options['sortByNumber'];
        $this->barWidth = $options['barWidth'];
        $this->hideSingleFindings = $options['hideSingleFindings'];
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function finish(): string
    {
        if ($this->sortByNumber) {
            arsort($this->tags);
        } else {
            ksort($this->tags);
        }

        $this->output->writeln(['', '', '<comment>Summary</comment>', '']);

        $rows = [];

        foreach ($this->tags as $tag => $count) {
            $percent = $count / $this->count;
            $bar = str_repeat('░', ceil($percent * $this->barWidth));
            if ($this->hideSingleFindings === false || $count > 1) {
                $rows[] = [$tag, $count, round($count / $this->count * 100) . ' %', $bar];
            }
        }


        $table = new Table($this->output);
        $table
            ->setHeaders(['Tag', 'Absolute', 'Percent', 'Graph'])
            ->setRows($rows);

        $table->render();

        return "";
    }
}
