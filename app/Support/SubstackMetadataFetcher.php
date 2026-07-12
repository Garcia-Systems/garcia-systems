<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Throwable;

class SubstackMetadataFetcher
{
    private const MAX_BYTES = 262144;

    public function fetch(string $url): array
    {
        if (! SubstackUrl::isAllowed($url) || $this->resolvesToUnsafeAddress($url)) {
            return [];
        }

        try {
            $response = Http::timeout(3)
                ->connectTimeout(2)
                ->withOptions(['allow_redirects' => ['max' => 2, 'strict' => true]])
                ->withHeaders(['User-Agent' => 'GarciaSystemsBot/1.0'])
                ->get($url);

            if (! $response->ok()) return [];

            $html = substr($response->body(), 0, self::MAX_BYTES);
            return [
                'image' => $this->meta($html, 'og:image'),
                'title' => $this->meta($html, 'og:title'),
                'description' => $this->meta($html, 'og:description'),
            ];
        } catch (Throwable) {
            return [];
        }
    }

    private function meta(string $html, string $property): ?string
    {
        $quoted = preg_quote($property, '/');
        if (preg_match('/<meta\s+[^>]*(?:property|name)=["\']'.$quoted.'["\'][^>]*content=["\']([^"\']+)["\'][^>]*>/i', $html, $m)
            || preg_match('/<meta\s+[^>]*content=["\']([^"\']+)["\'][^>]*(?:property|name)=["\']'.$quoted.'["\'][^>]*>/i', $html, $m)) {
            return html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5);
        }
        return null;
    }

    private function resolvesToUnsafeAddress(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) return true;
        if (filter_var($host, FILTER_VALIDATE_IP)) return $this->isUnsafeIp($host);
        $ips = gethostbynamel($host);
        if ($ips === false) return false;
        foreach ($ips as $ip) if ($this->isUnsafeIp($ip)) return true;
        return false;
    }

    private function isUnsafeIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
