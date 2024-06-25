<?php

namespace Startwind\WebInsights\Response\Html;

class HtmlDocument
{
    const SOURCE_HTML = 'html';
    const SOURCE_BODY = 'body';
    const SOURCE_CONTENT = 'content';

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

            $bodyWithoutTags = $this->removeTags($bodyWithoutTags, 'script');
            $bodyWithoutTags = $this->removeTags($bodyWithoutTags, 'style');
            $bodyWithoutTags = $this->removeTags($bodyWithoutTags, 'link');

            if ($bodyWithoutTags) {
                $this->bodyWithoutTags = $bodyWithoutTags;
            } else {
                $this->bodyWithoutTags = $plainContent;
            }
        } else {
            $this->body = $plainContent;
            $this->bodyWithoutTags = $plainContent;
        }
    }

    private function removeTags(string $html, string $tag) : string
    {
        $dom = new \DOMDocument();

        @$dom->loadHTML($html);

        $script = $dom->getElementsByTagName($tag);

        $remove = [];
        foreach ($script as $item) {
            $remove[] = $item;
        }

        foreach ($remove as $item) {
            $item->parentNode->removeChild($item);
        }

        return $dom->saveHTML();
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
                $results = array_merge($results, $matches[1]);
            }
        }

        return $results;
    }

    public function contains(string $string, bool $caseSensitive = false, $source = self::SOURCE_HTML): bool
    {
        if ($source === self::SOURCE_BODY) {
            $content = $this->body;
        } else if ($source === self::SOURCE_CONTENT) {
            $content = $this->bodyWithoutTags;
        } else {
            $content = $this->plainContent;
        }

        if (!$caseSensitive) {
            $string = strtolower($string);
            $htmlContent = strtolower($content);
        } else {
            $htmlContent = $content;
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

    public function countTextOccurrences($text): int
    {
        return substr_count($this->plainContent, $text);
    }
}
