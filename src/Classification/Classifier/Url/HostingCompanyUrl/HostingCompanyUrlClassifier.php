<?php

namespace Startwind\WebInsights\Classification\Classifier\Url\HostingCompanyUrl;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Classification\Classifier\UrlClassifier;

class HostingCompanyUrlClassifier extends UrlClassifier
{
    private const CLASSIFIER_PREFIX = 'company_type';

    private const CSV_FILE = __DIR__ . '/simple.txt';
    private const CSV_SEPARATOR = ',';

    private array $hosterList = [];

    public function __construct()
    {
        $this->initHostingList();
    }

    protected function doClassification(UriInterface $uri, array $existingTags): array
    {
        $host_names = explode(".", $uri->getHost());
        $domain = $host_names[count($host_names) - 2] . '.' . $host_names[count($host_names) - 1];

        if (array_key_exists($domain, $this->hosterList)) {
            return [self::CLASSIFIER_PREFIX . ':hosting_company'];
        } else {
            return [];
        }
    }

    private function initHostingList(): void
    {
        $count = 0;

        if (($handle = fopen(self::CSV_FILE, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, self::CSV_SEPARATOR)) !== FALSE) {
                $count++;
                if ($count > 1) {
                    if (array_key_exists(0, $data) && $data[0]) {
                        $url = parse_url($data[0]);
                        if (array_key_exists('host', $url)) {
                            $this->hosterList[$url['host']] = true;
                        }
                    }
                }
            }
            fclose($handle);
        }
    }
}
