<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Framework;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Framework\NodeJsClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\Html\HtmlDocument;
use Startwind\WebInsights\Response\HttpResponse;

class ReactClassifier implements Classifier
{
    const TAG = 'html:framework:react';
    const TAG_NEXT_JS = 'html:framework:nextjs';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if($httpResponse->headerContains('X-Powered-By', 'Next.js')) {
            return [
                self::TAG,
                self::TAG_NEXT_JS,
                NodeJsClassifier::TAG_PREFIX,
                ProgrammingLanguageClassifier::TAG_JS
            ];
        }

        return $this->doHtmlClassification($httpResponse->getHtmlDocument());
    }


    protected function doHtmlClassification(HtmlDocument $htmlDocument): array
    {
        if ($htmlDocument->containsAny([
            '__REACT_QUERY_STATE__',
            '__REDUX_STATE__',
            'data-react',
            'reactAppMount',
            'id="__next"'
        ])) {
            return [
                self::TAG,
                NodeJsClassifier::TAG_PREFIX,
                ProgrammingLanguageClassifier::TAG_JS
            ];
        } else {
            return [];
        }
    }
}
