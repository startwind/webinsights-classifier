<?php

namespace Startwind\WebInsights\Response\Html;

class HtmlDocument
{
    private string $plainContent;

    private string $body;
    private string $bodyWithoutTags;

    public function __construct(string $plainContent)
    {
        $this->plainContent = $plainContent;

        if (preg_match('~<body(.*)</body>~s', $this->plainContent, $matches)) {
            $body = $matches[1];
            $this->body = $body;

            $bodyWithoutTags = strip_tags($body, '<a><script><style>');

            $bodyWithoutTags = preg_replace('#<script(.*?)</script>#s', '', $bodyWithoutTags);
            $bodyWithoutTags = preg_replace('#<style(.*?)</style>#s', '', $bodyWithoutTags);

            $this->bodyWithoutTags = $bodyWithoutTags;
        } else {
            $this->body = $plainContent;
            $this->bodyWithoutTags = $plainContent;
        }
    }

    public function getPlainContent(): string
    {
        return $this->plainContent;
    }

    public function getBody($stripTags = false)
    {
        if ($stripTags) {
            return $this->bodyWithoutTags;
        } else {
            return $this->body;
        }
    }

    public function asDomDocument(): \DOMDocument
    {
        $dom = new \DOMDocument;

        if ($this->plainContent) {
            @$dom->loadHTML($this->plainContent);
        } else {
            $dom->loadHTML("<html></html>");
        }

        return $dom;
    }

    public function match($patterns): array
    {
        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        $results = [];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $this->plainContent, $matches);

            if (array_key_exists(1, $matches)) {
                $results = array_merge($matches[1]);
            }
        }

        return $results;
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
