<?php

namespace Startwind\WebInsights\Classification\Classifier\Http\Html\Content;

use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\ExtrasClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class EmailClassifier implements Classifier, ExtrasClassifier
{
    const TAG = 'html:content:email';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        $email_regex = '/[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,7}/';

        $tags = [];

        if (preg_match_all($email_regex, $httpResponse->getHtmlDocument()->getPlainContent(), $matches)) {

            foreach ($matches[0] as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) && !str_contains($email, '.png')) {
                    $tags[] = self::TAG;
                    $tags[] = self::TAG . ':' . $email;
                }
            }
        }

        return $tags;
    }
}
