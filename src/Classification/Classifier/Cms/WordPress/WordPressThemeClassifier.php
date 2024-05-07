<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms\WordPress;

use Startwind\WebInsights\Classification\Classifier\Hosting\WhmcsClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class WordPressThemeClassifier extends HtmlClassifier
{
    public const TAG = 'wordpress:theme:';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        $matches = $htmlDocument->match('^wp-content/themes/(.*?)/^');

        foreach ($matches as $match) {
            if (strlen($match) < 50) {
                $tags[] = self::TAG . str_replace(['"', ';', '<'], '', strtolower($match));
                $tags[] = WordPressClassifier::TAG;
                $tags[] = ProgrammingLanguageClassifier::TAG_PHP;

                if ($match === 'avada') {
                    $tags[] = WordPressPluginClassifier::TAG_PREFIX . 'avada';
                }

                if (in_array(strtolower($match), [
                        'hostiko', 'phox', 'hostie', 'hostcluster', 'hoskia', 'bluishost', 'hostinza', 'hostix', 'alaska', 'hostbridge',
                        'hostingo', 'ecohosting', 'hostwhmcs', 'virtusky', 'zionhost', 'maxhost', 'nexbunker', 'kripdom', 'hostingpress',
                        'singara', 'hibreed', 'hosted', 'aoxhost', 'slake', 'zipprich', 'colorhost', 'hostpro', 'hostwind', 'qloud', 'rockethost',
                        'cloudy7', 'hostcloud', 'elastix', 'hostino', 'hostio', 'bluerack', 'hosbit', 'hostlab', 'umlimhost', 'hostme',
                        'cloudme', 'onehost', 'servereast', 'emyui', 'unihost', 'truehost', 'cloudhost', 'dataserv', 'hostlinea', 'hoststar',
                        'hoka'
                    ]
                )) {
                    $tags[] = WhmcsClassifier::TAG;
                }
            }
        }

        return $tags;
    }
}
