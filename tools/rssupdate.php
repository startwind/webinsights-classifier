<?php

use GuzzleHttp\RequestOptions;
use Startwind\WebInsights\Classification\Classifier\Hosting\HostingProductsClassifier;

include_once __DIR__ . '/../vendor/autoload.php';

$productClassifier = new HostingProductsClassifier();

function getTags(string $title, string $description)
{
    global $productClassifier;

    $httpResponse = new \Startwind\WebInsights\Response\HttpResponse(
        $title . ' ' . $description,
        [],
        200,
        new \GuzzleHttp\Psr7\Uri('https://example.com'),
        100,
        '0.0.0.0'
    );

    $tags = $productClassifier->classify($httpResponse, []);

    $shortTags = [];

    foreach ($tags as $tag) {
        $shortTags[] = str_replace(HostingProductsClassifier::TAG_PREFIX, '', $tag);
    }

    return array_unique($shortTags);
}

$client = new \GuzzleHttp\Client();

if (count($argv) === 3) {
    $domains = [
        [
            'uri' => $argv[1],
            'extras' => ['blog:feed' => $argv[2]],
        ]
    ];
} else {

    $url = 'https://api.webinsights.info/extras/uris';

    $payload = [
        'extras' => [
            "blog:feed"
        ]
    ];

    $response = $client->post($url, [RequestOptions::JSON => $payload]);

    $result = json_decode((string)$response->getBody(), true);

    $domains = $result['data']['domains'];
}

$count = 0;

foreach ($domains as $domain) {
    $count++;

    $url = $domain['uri'];
    $feed = $domain['extras']['blog:feed'];

    try {
        $response = $client->get($feed, [
            RequestOptions::TIMEOUT => 2,
            RequestOptions::CONNECT_TIMEOUT => 2
        ]);
    } catch (Exception $exception) {
        var_dump('Fehler: ' . $exception->getMessage());
        continue;
    }

    $xml = @simplexml_load_string($response->getBody());

    echo "\nFetching: " . $feed . "\n";

    $interval = '1m';

    if ($xml && $xml->channel && $xml->channel->item && $xml->channel->item->count() > 0) {
        $first = true;
        $rssItems = [];

        foreach ($xml->channel->item as $item) {
            $pubDate = strtotime((string)$item->pubDate);
            if ($first) {
                $agoInDays = (int)((time() - $pubDate) / 60 / 60 / 24);
                if ($agoInDays < 365) $interval = '1w';
                if ($agoInDays < 100) $interval = '1d';

                echo "\n Suggested interval: " . $interval . "\n\n";
            }

            $first = false;

            $tags = getTags((string)$item->title, (string)$item->description);

            echo " - " . $item->title . " (date: " . date('Y-m-d H:i:s', $pubDate) . ")\n";
            echo "   " . $item->link . "\n";
            if (count($tags) > 0) {
                echo "   tags: " . implode(', ', $tags) . "\n\n";
            }

            $title = strtolower((string)$item->title);

            $rssItem = [
                'title' => (string)$item->title,
                'pubDate' => $pubDate,
                'link' => (string)$item->link,

            ];

            if (count($tags) > 0) {
                $rssItem['tags'] = $tags;
            }

            $rssItems[] = $rssItem;
        }

        $response = $client->post('https://api.webinsights.info/rss/add', [
            RequestOptions::JSON => [
                'uri' => $url,
                'items' => $rssItems
            ]
        ]);

        $result = json_decode((string)$response->getBody(), true);

        echo "\n UPLOAD: " . $result['message'] . "\n";
    }
}

