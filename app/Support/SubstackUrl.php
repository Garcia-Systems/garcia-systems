<?php

namespace App\Support;

class SubstackUrl
{
    public static function isAllowed(?string $url): bool
    {
        if (! is_string($url) || trim($url) === '') return false;
        $parts = parse_url($url);
        if (($parts['scheme'] ?? null) !== 'https') return false;
        $host = strtolower($parts['host'] ?? '');
        return $host === 'substack.com' || str_ends_with($host, '.substack.com');
    }
}
