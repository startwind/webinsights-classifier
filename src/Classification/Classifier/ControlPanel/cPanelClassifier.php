<?php

namespace Startwind\WebInsights\Classification\Classifier\ControlPanel;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\HttpResponse;

class cPanelClassifier implements Classifier
{
    const TAG = 'control_panel:system:cpanel';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        return [];
    }
}
