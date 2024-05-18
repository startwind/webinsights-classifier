<?php

use GuzzleHttp\RequestOptions;

include_once __DIR__ . '/../vendor/autoload.php';

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
            echo " - " . $item->title . " (date: " . date('Y-m-d H:i:s', $pubDate) . ")\n";
            echo "   " . $item->link . "\n\n";

            $rssItems[] = [
                'title' => (string)$item->title,
                'pubDate' => $pubDate,
                'link' => (string)$item->link
            ];
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

