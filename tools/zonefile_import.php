<?php

use Startwind\WebInsights\Hosting\AS\CompositeAsExtractor;
use Startwind\WebInsights\Hosting\AS\CymruAsExtractor;
use Startwind\WebInsights\Hosting\IpRange\CompositeIpRangeExtractor;
use Startwind\WebInsights\Hosting\IpRange\HackerTargetIpRangeExtractor;
use Startwind\WebInsights\Hosting\IpRange\RadbWhoisIpRangeExtractor;

include_once __DIR__ . '/../vendor/autoload.php';

if (array_key_exists(1, $argv)) {
    $filename = $argv[1];
} else {
    $filename = __DIR__ . '/zonefile.txt';
}

if (array_key_exists(2, $argv)) {
    $startWith = $argv[2];
} else {
    $startWith = 0;
}

$logger = new \Startwind\WebInsights\Logger\FileLogger(\Psr\Log\LogLevel::INFO, '/tmp/as');

$ipRangeExtractor = new CompositeIpRangeExtractor();
$ipRangeExtractor->setLogger($logger);

$ipRangeExtractor->addExtractor(new HackerTargetIpRangeExtractor());
$ipRangeExtractor->addExtractor(new RadbWhoisIpRangeExtractor());

$asExtractor = new CompositeAsExtractor();
$asExtractor->setLogger($logger);

$asExtractor->addExtractor(new CymruAsExtractor());

$asExporter = new \Startwind\WebInsights\Hosting\Export\ApiExporter();

function getAsn($ip): int
{
    global $asCollection;
    global $asExtractor;
    global $ipRangeExtractor;
    global $asExporter;

    $longIp = ip2long($ip);

    $query = [
        'ranges' => [
            '$elemMatch' => [
                'from' => ['$lte' => $longIp],
                'to' => ['$gte' => $longIp],
            ]
        ]
    ];

    $as = $asCollection->findOne($query);

    if ($as) {
        return $as['as'];
    } else {
        var_dump('NOT FOUND ' . $ip);
    }

    $as = $asExtractor->getAs($ip);

    var_dump($as);

    $ranges = $ipRangeExtractor->getIpRange($as);

    if ($as && $as != 'NA') {
        $asExporter->export($as, $ranges);
    }

    return (int)$as;
}

$asn = [];

$handle = fopen($filename, 'r');

$count = 0;
$blockSize = 2000;
$found = 0;

$documents = [];

$mongoDBUrl = "mongodb://localhost:27017";

$client = new \MongoDB\Client($mongoDBUrl);

$database = $client->selectDatabase('classifier');

$collection = $database->selectCollection('internet');
$asCollection = $database->selectCollection('as');

$domains = [];
$operations = [];

function processData($domains, $documents): void
{
    global $collection;

    $operations = [];

    $knownDomains = $collection->find(['domain' => ['$in' => $domains]]);

    foreach ($knownDomains as $knownDomain) {
        if ($knownDomain['ip'] != $documents[$knownDomain['domain']]['ip']) {

            $as = getAsn($documents[$knownDomain['domain']['ip']]);

            $historyIp = [
                'date' => new \MongoDB\BSON\UTCDateTime(),
                'value' => $documents[$knownDomain['domain']['ip']]
            ];

            if ($as != $knownDomain['as']) {
                $historyAs = [
                    'date' => new \MongoDB\BSON\UTCDateTime(),
                    'value' => $as
                ];
                $operations[] = ['updateOne' => [['_id' => $knownDomain['_id']], ['$push' => ['history.ip' => $historyIp, 'history.as' => $historyAs]]]];
            } else {
                $operations[] = ['updateOne' => [['_id' => $knownDomain['_id']], ['$push' => ['history.ip' => $historyIp]]]];
            }
        }

        unset($documents[$knownDomain['domain']]);
    }

    foreach ($documents as $document) {
        $as = getAsn($document['ip']);

        $document['as'] = $as;
        $document['history']['as'][] = [
            [
                'date' => new \MongoDB\BSON\UTCDateTime(),
                'value' => $as
            ]
        ];

        $operations[] = ['insertOne' => [$document]];
    }

    if (count($operations) > 0) {
        $collection->bulkWrite($operations);
    }
}

while ($data = fgetcsv($handle)) {
    $count++;
    if ($count >= $startWith) {
        $ip = $data[2];
        $domain = $data[0];

        if ($ip) {
            $domains[] = $domain;
            $found++;

            $documents[$domain] = [
                'domain' => $domain,
                'ip' => $ip,
                'as' => false,
                'discovery_date' => $data[11],
                'history' => [
                    'ip' => [
                        [
                            'date' => new \MongoDB\BSON\UTCDateTime(),
                            'value' => $ip
                        ]
                    ],
                    'as' => [

                    ]
                ]
            ];

            if ($found % $blockSize == 0) {
                echo "\nPersisting dataset #" . $count;
                processData($domains, $documents);

                $documents = [];
                $domains = [];
            }
        }
    }
}

echo "\nPersisting dataset #" . $count;
processData($domains, $documents);
