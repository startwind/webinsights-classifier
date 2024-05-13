<?php

namespace Startwind\WebInsights\Classification\Classifier\Rss;

use GuzzleHttp\Client;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\Cms\WordPress\WordPressClassifier;
use Startwind\WebInsights\Classification\Classifier\Hosting\HostingCompanyClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Content\PageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class RssFeedClassifier implements Classifier
{
    const TAG_PREFIX = 'rss:url:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if (in_array(HostingCompanyClassifier::CLASSIFIER_PREFIX, $existingTags)
            && in_array(PageClassifier::TAG_BLOG, $existingTags)) {

            $client = new Client();

            $requestUri = $httpResponse->getRequestUri();

            $domain = $requestUri->getScheme() . '://' . $requestUri->getHost();

            if (in_array(WordPressClassifier::TAG, $existingTags)) {
                $feedUrl = $domain . '/feed';
                try {
                    $response = $client->get($feedUrl);

                    $xml = simplexml_load_string($response->getBody());

                    if ($xml->channel->item->count() > 3) {
                        return [self::TAG_PREFIX . urlencode($feedUrl)];
                    } else {
                        return [];
                    }

                } catch (\Exception) {

                }
            }

        }

        return [];
    }
}
