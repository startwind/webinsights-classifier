<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class ProgrammingLanguageClassifier extends HttpClassifier
{
    const TAG_PREFIX = 'tech:language:';

    public const TAG_PHP = self::TAG_PREFIX . 'php';
    public const TAG_JS = self::TAG_PREFIX . 'js';

    public const TAG_PERL = self::TAG_PREFIX . 'perl';
    public const TAG_SCALA = self::TAG_PREFIX . 'scala';
    public const TAG_JAVA = self::TAG_PREFIX . 'java';

    protected function doHttpClassification(HttpResponse $response): array
    {
        if ($response->headerContains('X-Powered-By', 'php')) {
            return [self::TAG_PHP];
        } else {
            return [];
        }
    }
}
