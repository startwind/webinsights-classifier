<?php

namespace Startwind\WebInsights\Classification\Classifier\Rss;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Startwind\WebInsights\Classification\Classifier\Classifier;
use Startwind\WebInsights\Classification\Classifier\ExtrasClassifier;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Content\PageClassifier;
use Startwind\WebInsights\Response\HttpResponse;

class RssFeedClassifier implements Classifier, ExtrasClassifier
{
    const TAG_FEED = Classifier::TAG_PREFIX_EXTRA . ':blog:feed:';
    const TAG_URL = Classifier::TAG_PREFIX_EXTRA . ':blog:url:';

    public function classify(HttpResponse $httpResponse, array $existingTags): array
    {
        if (true || in_array(PageClassifier::TAG_BLOG, $existingTags)) {

            $client = new Client();

            $requestUri = $httpResponse->getRequestUri();

            $originDomain = $requestUri->getScheme() . '://' . $requestUri->getHost();

            if ($httpResponse->getHtmlDocument()->contains('//blog.')) {
                $domain = $requestUri->getScheme() . '://blog.' . $requestUri->getHost();
            } else if ($httpResponse->getHtmlDocument()->contains('/blog')) {
                $domain = $requestUri->getScheme() . '://' . $requestUri->getHost() . '/blog';
            } else {
                $domain = $originDomain;
            }

            $feedUrl = $domain . '/feed';

            try {
                $response = $client->get($feedUrl, [
                    RequestOptions::TIMEOUT => 2,
                    RequestOptions::CONNECT_TIMEOUT => 2
                ]);

                $xml = @simplexml_load_string($response->getBody());

                if ($xml && $xml->channel && $xml->channel->item && $xml->channel->item->count() > 0) {
                    return [self::TAG_FEED . $feedUrl];
                }

                return [];
            } catch (\Exception) {

            }

            if ($domain != $originDomain) {
                try {
                    $client->get($domain, [
                        RequestOptions::TIMEOUT => 2,
                        RequestOptions::CONNECT_TIMEOUT => 2
                    ]);

                    return [self::TAG_URL . $domain];
                } catch (\Exception) {
                }
            }
        }

        return [];
    }
}
