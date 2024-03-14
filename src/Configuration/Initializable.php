<?php

namespace Startwind\WebInsights\Configuration;

interface Initializable
{
    public const DEFAULT_FIELD_CLASS = 'class';
    public const DEFAULT_FIELD_OPTIONS = 'options';

    public function __construct(array $options = []);
}
