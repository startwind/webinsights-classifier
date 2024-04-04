<?php

const CLASSIFIER_VERSION = '0.2.0';

use Startwind\WebInsights\Application\Aggregation\AggregateCommand;
use Startwind\WebInsights\Application\Aggregation\AggregatePopCommand;
use Startwind\WebInsights\Application\Classification\ClassifyCommand;
use Startwind\WebInsights\Application\Classification\ClassifyManyCommand;
use Startwind\WebInsights\Application\Hosting\GetIpRangeCommand;
use Symfony\Component\Console\Application;

include_once __DIR__ . '/../vendor/autoload.php';


$application = new Application('WebInsights Classifier by Nils Langner', CLASSIFIER_VERSION);

$application->add(new ClassifyCommand());
$application->add(new ClassifyManyCommand());

$application->add(new AggregateCommand());
$application->add(new AggregatePopCommand());

$application->add(new GetIpRangeCommand());

$application->run();
