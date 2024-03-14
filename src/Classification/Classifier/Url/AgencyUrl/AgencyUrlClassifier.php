<?php

namespace Startwind\WebInsights\Classification\Classifier\Url\AgencyUrl;

use Psr\Http\Message\UriInterface;
use Startwind\WebInsights\Classification\Classifier\UrlClassifier;

class AgencyUrlClassifier extends UrlClassifier
{
    private const CLASSIFIER_PREFIX = 'company_type';

    private const CSV_FILES = [
        __DIR__ . '/agency_de.csv'
    ];

    private const CSV_SEPARATOR = ',';

    private array $agencyList = [];

    protected function doClassification(UriInterface $uri, array $existingTags): array
    {
        if (count($this->agencyList) === 0) {
            $this->initAgencyList();
        }

        $host_names = explode(".", $uri->getHost());
        $domain = $host_names[count($host_names) - 2] . '.' . $host_names[count($host_names) - 1];

        if (in_array($domain, $this->agencyList)) {
            return [self::CLASSIFIER_PREFIX . ':agency'];
        } else {
            return [];
        }
    }

    private function initAgencyList()
    {
        $count = 0;

        foreach (self::CSV_FILES as $csvFile) {
            if (($handle = fopen($csvFile, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, self::CSV_SEPARATOR)) !== FALSE) {
                    $count++;
                    if ($count > 1) {
                        if (array_key_exists(0, $data) && $data[0]) {
                            $this->agencyList[] = str_replace('www.', '', $data[0]);
                        }
                    }
                }
                fclose($handle);
            }
        }
    }
}
