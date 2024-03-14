<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Http\Cdn;

use Startwind\WebInsights\Classification\Classifier\Http\HttpClassifier;

abstract class CDNClassifier extends HttpClassifier
{
    const TAG_PREFIX = 'tech:cdn:';
}
