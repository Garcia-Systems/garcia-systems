<?php

namespace App\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;

class SubstackEmbed
{
    private const SCRIPT_SRC = 'https://substack.com/embedjs/embed.js';

    public static function sanitize(?string $html): ?string
    {
        $html = trim((string) $html);
        if ($html === '') return null;
        if (Str::contains(Str::lower($html), ['<iframe', 'javascript:', 'data:', 'onerror=', 'onclick='])) throw new InvalidArgumentException('Only official Substack embed markup is allowed.');
        $script = '';
        if (preg_match_all('/<script\b[^>]*><\/script>/i', $html, $scripts)) {
            foreach ($scripts[0] as $tag) {
                if (! preg_match('/\bsrc=["\']https:\/\/substack\.com\/embedjs\/embed\.js["\']/i', $tag)) throw new InvalidArgumentException('Only the official Substack embed script is allowed.');
            }
            $script = '<script async src="'.self::SCRIPT_SRC.'" charset="utf-8"></script>';
            $html = preg_replace('/<script\b[^>]*><\/script>/i', '', $html);
        }
        $html = strip_tags($html, '<div><p><a><span><strong><em><br>');
        $html = preg_replace('/\s(on\w+|style|id)=("[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $html);
        $html = preg_replace_callback('/<a\b([^>]*)>/i', function ($matches) {
            if (! preg_match('/\bhref=["\']([^"\']+)["\']/i', $matches[1], $href)) throw new InvalidArgumentException('Substack embeds must include a Substack link.');
            if (! self::isAllowedUrl($href[1])) throw new InvalidArgumentException('Substack links must point to Substack domains.');
            return '<a href="'.e($href[1]).'" target="_blank" rel="noopener noreferrer">';
        }, $html);
        $html = preg_replace_callback('/<div\b([^>]*)>/i', function ($matches) {
            if (preg_match('/\bclass=["\']([^"\']*)["\']/i', $matches[1], $class) && Str::contains($class[1], 'substack')) return '<div class="'.e($class[1]).'">';
            return '<div>';
        }, $html);
        if (! self::extractUrl($html)) throw new InvalidArgumentException('Substack embeds must include a Substack link.');
        return trim($html).($script ? "\n".$script : '');
    }

    public static function extractUrl(?string $html): ?string
    {
        if (preg_match('/\bhref=["\']([^"\']+)["\']/i', (string) $html, $matches) && self::isAllowedUrl($matches[1])) return $matches[1];
        return null;
    }

    public static function bodyWithoutScript(?string $html): ?string
    {
        return trim((string) preg_replace('/<script\b[^>]*><\/script>/i', '', (string) $html));
    }

    public static function hasScript(?string $html): bool
    {
        return preg_match('/<script\b[^>]*src=["\']https:\/\/substack\.com\/embedjs\/embed\.js["\'][^>]*><\/script>/i', (string) $html) === 1;
    }

    private static function isAllowedUrl(string $url): bool
    {
        $host = Str::lower(parse_url($url, PHP_URL_HOST) ?: '');
        return Str::startsWith($url, 'https://') && ($host === 'substack.com' || Str::endsWith($host, '.substack.com'));
    }
}
