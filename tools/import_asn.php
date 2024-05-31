<?php

include_once __DIR__ . '/../vendor/autoload.php';

$dir = $argv[1];

if (!is_dir($dir)) {
    echo "The directory $dir does not exist.";
    return;
}

// Create a DirectoryIterator object
$iterator = new DirectoryIterator($dir);

// Loop through the directory
foreach ($iterator as $fileInfo) {
    // Skip . and ..
    if ($fileInfo->isDot()) {
        continue;
    }

    // Print the current item's name
    if ($fileInfo->isDir()) {

        $asInfo = json_decode(file_get_contents($dir . '/' . $fileInfo->getFilename() . '/aggregated.json'));

        var_dump($asInfo);

        die;
    }

}

