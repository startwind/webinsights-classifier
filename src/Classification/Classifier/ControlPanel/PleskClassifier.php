<?php

namespace Startwind\WebInsights\Classification\Classifier\ControlPanel;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class PleskClassifier implements Classifier
{
    const TAG_PLESK = 'control_panel:system:plesk';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->headerContains('X-Powered-By', 'Plesk')) {
            return [self::TAG_PLESK];
        } else {
            return [];
        }
    }

}
