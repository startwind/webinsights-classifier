<?php

include_once __DIR__ . '/../vendor/autoload.php';

$mongoDBUrl = "mongodb://localhost:27017";

$client = new \MongoDB\Client($mongoDBUrl);

$database = $client->selectDatabase('classifier');

$collection = $database->selectCollection('internet');

$iteration = 0;
$blockSize = 5000;
$operations = [];

$query = [
    'history.as.date' => [
        '$gt' => new MongoDB\BSON\UTCDateTime(new DateTime('2024-06-02T00:00:00.000Z'))
    ],
    'lastAs' => ['$exists' => false]
];

while ($domains = $collection->find($query, ['skip' => $iteration * $blockSize, 'limit' => $blockSize])) {
    $iteration++;

    foreach ($domains as $domain) {
        $domainArray = json_decode(json_encode($domain), true);

        if (count($domainArray['history']['as']) > 1) {
            if ($domainArray['history']['as'][count($domainArray['history']['as']) - 1]['value'] != $domainArray['as']) {
                $operations[] = ['updateOne' => [['domain' => $domainArray['domain']], ['$set' => ['as' => $domainArray['history']['as'][count($domainArray['history']['as']) - 1]['value'], 'lastAs' => $domainArray['as']]]]];
            }
        }
    }

    if (count($operations) > 0) {
        $collection->bulkWrite($operations);
    }

    $operations = [];

    echo "Block " . $iteration * $blockSize . "\n";
}

