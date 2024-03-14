<?php

namespace Startwind\WebInsights\Util;

use MongoDB\Client;
use MongoDB\Collection;

abstract class MongoDBHelper
{
    public const DEFAULT_SERVER = 'localhost';
    public const DEFAULT_PORT = "27017";

    static public function getCollection(string $database, string $collection, string $server = self::DEFAULT_SERVER, string $port = self::DEFAULT_PORT): Collection
    {
        $mongoDBUrl = "mongodb://" . $server . ':' . $port;

        $client = new Client($mongoDBUrl);

        $database = $client->selectDatabase($database);

        return $database->selectCollection($collection);
    }
}
