<?php

namespace Startwind\WebInsights\Response\Html;

class HtmlDocument
{
    private string $plainContent;

    public function __construct(string $plainContent)
    {
        $this->plainContent = $plainContent;
    }

    public function getPlainContent(): string
    {
        return $this->plainContent;
    }

    public function asDomDocument(): \DOMDocument
    {
        $dom = new \DOMDocument;

        if ($this->plainContent) {
            @$dom->loadHTML($this->plainContent);
        }else{
            $dom->loadHTML("<html></html>");
        }

        return $dom;
    }

    public function match($pattern): array
    {
        preg_match_all($pattern, $this->plainContent, $matches);

        if (array_key_exists(1, $matches)) {
            return $matches[1];
        } else {
            return [];
        }
    }

    public function contains(string $string, bool $caseSensitive = false): bool
    {
        if (!$caseSensitive) {
            $string = strtolower($string);
            $htmlContent = strtolower($this->plainContent);
        } else {
            $htmlContent = $this->plainContent;
        }

        return str_contains($htmlContent, $string);
    }

    public function containsAny(array $strings, bool $caseSensitive = false): bool
    {
        foreach ($strings as $string) {
            if ($this->contains($string, $caseSensitive)) {
                return true;
            }
        }

        return false;
    }

    public function containsRegEx(string $pattern): bool
    {
        return preg_match($pattern, strtolower($this->plainContent)) > 0;
    }
}
