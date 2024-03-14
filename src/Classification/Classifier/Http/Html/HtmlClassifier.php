<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;
use Startwind\WebInsights\Response\HttpResponse;

abstract class HtmlClassifier implements Classifier
{
    public function classify(HttpResponse $httpResponse, array $existingTags = []): array
    {
        return $this->doHtmlClassification($httpResponse->getHtmlDocument());
    }

    abstract protected function doHtmlClassification(HtmlDocument $htmlDocument): array;
}
