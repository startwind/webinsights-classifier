<?php

namespace Startwind\WebInsights\Hosting\AS;

class CymruAsExtractor implements AsExtractor
{
    public function getAs(string $domain): string
    {
        if (!filter_var($domain, FILTER_VALIDATE_IP)) {
            $ip = gethostbyname($domain);
        } else {
            $ip = $domain;
        }

        exec("whois -h whois.cymru.com -- '-v " . $ip . "'", $output);

        $entryLine = $output[1];
        $entryElements = explode(' ', $entryLine);

        return $entryElements[0];
    }

}
