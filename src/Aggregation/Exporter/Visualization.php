<?php

namespace Startwind\WebInsights\Aggregation\Exporter;

abstract class Visualization
{
    public const TYPE_DEFAULT = 'default';
    public const TYPE_LIST_IMAGE = 'image_list';
    public const TYPE_LIST_TABLE = 'table';
    public const TYPE_LIST_TABLE_ENRICHED = 'table_enriched';
}
