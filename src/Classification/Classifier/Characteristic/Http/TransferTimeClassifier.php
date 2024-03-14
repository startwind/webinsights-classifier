<?php

namespace Startwind\WebInsights\Classification\Classifier\Characteristic\Http;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;
use Startwind\WebInsights\Response\HttpResponse;

class TransferTimeClassifier implements Classifier
{
    const TAG = 'characteristic:http:transfer_time_ms:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $time = $httpResponse->getTransferTimeInMs();

        if ($time == 0) {
            return [];
        }

        if ($time <= 100) {
            return [self::TAG . '100'];
        }

        if ($time <= 500) {
            return [self::TAG . '500'];
        }

        if ($time <= 1000) {
            return [self::TAG . '1000'];
        }

        if ($time <= 2000) {
            return [self::TAG . '2000'];
        }

        if ($time <= 5000) {
            return [self::TAG . '5000'];
        }

        if ($time <= 10000) {
            return [self::TAG . '10000'];
        }

        if ($time <= 20000) {
            return [self::TAG . '20000'];
        }

        return [];
    }


    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->getPlainContent() === "") {
            return [];
        }
        $sizeInKb = mb_strlen($htmlDocument->getPlainContent(), '8bit');
        $sizeInMb = ceil($sizeInKb / 1000 / 1000);

        return [self::TAG . ':' . $sizeInMb . '_MB'];
    }
}
