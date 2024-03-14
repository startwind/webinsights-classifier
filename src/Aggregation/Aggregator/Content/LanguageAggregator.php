<?php

namespace Startwind\WebInsights\Aggregation\Aggregator\Content;

use Startwind\WebInsights\Aggregation\Aggregator\CountingAggregator;
use Startwind\WebInsights\Classification\ClassificationResult;
use Startwind\WebInsights\Classification\Classifier\Http\Html\Language\LanguageClassifier;

class LanguageAggregator extends CountingAggregator
{
    protected string $name = "Content Language Distribution";

    protected string $description = "This distribution is based of the meta information found in the HTML files. It includes among other things meta tags, hreflang settings and open graph information.";

    public const FIELD_MAIN_LANGUAGE = 'main_language';
    public const FIELD_DETAIL_LANGUAGE = 'detail_language';

    public function aggregate(ClassificationResult $classificationResult): void
    {
        $languages = $classificationResult->getTagsStartingWithString(LanguageClassifier::TAG_PREFIX, true);

        $addedLanguages = [];

        foreach ($languages as $language) {
            if (str_contains($language, '-')) {
                // $this->increaseCount($language, self::FIELD_DETAIL_LANGUAGE);
                $languageArray = explode('-', $language);
                $language = $languageArray[0];
            } else if (str_contains($language, '_')) {
                // $this->increaseCount($language, self::FIELD_DETAIL_LANGUAGE);
                $languageArray = explode('_', $language);
                $language = $languageArray[0];
            }

            if (in_array($language, $addedLanguages)) continue;
            $addedLanguages[] = $language;

            $language = $this->country2flag($language) . '&nbsp;&nbsp;' . \Locale::getDisplayLanguage($language) . ' (' . $language . ')';

            $this->increaseCount($language, /* self::FIELD_MAIN_LANGUAGE */);
        }
    }

    private function country2flag(string $countryCode): string
    {
        if ($countryCode == "en") $countryCode = "gb";
        if ($countryCode == "ar") $countryCode = "sa";
        if ($countryCode == "zh") $countryCode = "cn";
        if ($countryCode == "ja") $countryCode = "jp";
        if ($countryCode == "ko") $countryCode = "kr";
        if ($countryCode == "el") $countryCode = "gr";
        if ($countryCode == "cs") $countryCode = "cz";
        if ($countryCode == "da") $countryCode = "dk";
        if ($countryCode == "uk") $countryCode = "ua";

        return (string)preg_replace_callback(
            '/./',
            static fn(array $letter) => mb_chr(ord($letter[0]) % 32 + 0x1F1E5),
            $countryCode
        );
    }
}
