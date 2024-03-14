<?php

use MongoDB\Client;

include_once __DIR__ . '/../vendor/autoload.php';

$mongoDBUrl = "mongodb://localhost:27017";

$client = new Client($mongoDBUrl);
$database = $client->selectDatabase('classifier');

$collection = $database->selectCollection('raw_data');
$elements = $collection->find(['tags' => ['$type' => "object"]]);

$count = 0;

foreach ($elements as $element) {
    echo($count . ' - ' . $element->_id . "\n");
    $count++;
    $tags = json_decode(json_encode($element->tags), true);
    $collection->updateOne(['_id' => $element->_id], ['$set' => ["tags" => array_values($tags)]]);
}
