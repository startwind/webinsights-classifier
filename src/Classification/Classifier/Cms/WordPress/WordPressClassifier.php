<?php

namespace Startwind\WebInsights\Classification\Classifier\Cms\WordPress;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Cms\CmsClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Http\ProgrammingLanguageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class WordPressClassifier implements Classifier
{
    public const  TAG = CmsClassifier::CLASSIFIER_PREFIX . 'wordpress';
    private const TAG_WP_ADMIN = 'cms:system:wordpress:admin';

    private bool $isDetailedClassification = false;

    public function init(array $options): void
    {
        if (array_key_exists('detailed', $options)) {
            $this->isDetailedClassification = $options['detailed'];
        }
    }

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if ($httpResponse->getHtmlDocument()->contains('wp-content/')) {
            return [self::TAG, ProgrammingLanguageClassifier::TAG_PHP];
        }

        if ($this->isDetailedClassification && $this->checkAdmin($httpResponse->getRequestUri())) {
            return [self::TAG, self::TAG_WP_ADMIN, ProgrammingLanguageClassifier::TAG_PHP];
        }

        return [];
    }

    protected function checkAdmin(UriInterface $uri): bool
    {
        $loginUri = $uri->withPath('/wp-login.php');

        try {
            $response = $this->getHttpClient()->get($loginUri, [RequestOptions::ALLOW_REDIRECTS => false]);
        } catch (\Exception $exception) {
            return false;
        }

        return $response->getStatusCode() === 200;
    }
}
