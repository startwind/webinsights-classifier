<?php

include_once __DIR__ . '/../vendor/autoload.php';

$dir = $argv[1];

if (!is_dir($dir)) {
    echo "The directory $dir does not exist.";
    return;
}

// Create a DirectoryIterator object
$iterator = new DirectoryIterator($dir);

$blockSize = 500;

$export = new \Startwind\WebInsights\Hosting\Export\ApiExporter();

$count = 0;

// Loop through the directory
foreach ($iterator as $fileInfo) {
    // Skip . and ..
    if ($fileInfo->isDot()) {
        continue;
    }

    // Print the current item's name
    if ($fileInfo->isDir()) {
        $count++;

        $as = $fileInfo->getFilename();

        $asInfo = json_decode(file_get_contents($dir . '/' . $as . '/aggregated.json'), true);

        $ip4 = $asInfo['subnets']['ipv4'];
        $handle = $asInfo['handle'];
        $description = $asInfo['description'];

        if (count($ip4) > 0) {
            $export->exportMany([[$as => ['ipRanges' => $ip4, 'handle' => $handle, 'description' => $description]]]);
        }

        echo "# $count | " . $as . "\n";
    }

}

