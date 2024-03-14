<?php

namespace Startwind\WebInsights\Application\Aggregation;

use Startwind\WebInsights\Aggregation\Configuration\AggregationConfiguration;
use Startwind\WebInsights\Util\MongoDBHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'aggregate-pop',
    description: 'Aggregate classified websites.',
    hidden: false
)]
class AggregatePopCommand extends AggregationCommand
{
    const QUEUE_STATUS_QUEUED = 'queued';
    const QUEUE_STATUS_FINISHED = 'finished';
    const QUEUE_STATUS_RUNNING = 'running';
    const QUEUE_STATUS_FAILED = 'failed';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collection = MongoDBHelper::getCollection('classifier', 'builder');

        // @todo via API
        $job = $collection->findOne(['status' => self::QUEUE_STATUS_QUEUED], ['sort' => ['_id' => 1]]);

        if (is_null($job)) {
            $output->writeln('<info>No element in queue found.</info>');
            return Command::FAILURE;
        }

        $collection->updateOne(['_id' => $job['_id']], ['$set' => ['status' => self::QUEUE_STATUS_RUNNING, 'timing.started' => date('Y-m-d H:i:s')]]);

        $config = json_decode(json_encode($job['config']), true);

        $this->configuration = AggregationConfiguration::fromArray($config, $output);

        try {
            $count = $this->doExecute();
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            $collection->updateOne(['_id' => $job['_id']], ['$set' => ['status' => self::QUEUE_STATUS_FAILED, 'error' => $exception->getMessage(), 'timing.finished' => date('Y-m-d H:i:s')]]);
            return Command::FAILURE;
        }

        $collection->updateOne(['_id' => $job['_id']], ['$set' => ['count' => $count, 'status' => self::QUEUE_STATUS_FINISHED, 'timing.finished' => date('Y-m-d H:i:s')]]);

        return Command::SUCCESS;
    }
}
