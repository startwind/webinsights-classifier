<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content;

use Startwind\WebInsights\Classification\Classifier\Http\Html\HtmlClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;

class NewsletterClassifier extends HtmlClassifier
{
    const TAG = 'html:content:newsletter:';

    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        $tags = [];

        if ($htmlDocument->containsAny([' action="https://my.sendinblue.com/users/subscribeembed'])) {
            $tags[] = self::TAG . 'sendinblue';
        }

        return $tags;
    }
}
