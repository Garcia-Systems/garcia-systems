<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'url',
        'thumbnail_url',
        'description',
        'transcript',
        'article_id',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function getYoutubeVideoIdAttribute(): ?string
    {
        return self::parseYoutubeVideoId($this->url);
    }

    public function getEmbedUrlAttribute(): ?string
    {
        return $this->youtube_video_id ? 'https://www.youtube-nocookie.com/embed/'.$this->youtube_video_id : null;
    }

    public function getWatchUrlAttribute(): string
    {
        return $this->youtube_video_id ? 'https://www.youtube.com/watch?v='.$this->youtube_video_id : $this->url;
    }

    public function getDisplayThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_url ?: ($this->youtube_video_id ? 'https://i.ytimg.com/vi/'.$this->youtube_video_id.'/hqdefault.jpg' : null);
    }

    public static function parseYoutubeVideoId(?string $url): ?string
    {
        $parts = parse_url((string) $url);
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');
        $id = null;

        if (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            if ($path === 'watch') {
                parse_str($parts['query'] ?? '', $query);
                $id = $query['v'] ?? null;
            } elseif (str_starts_with($path, 'embed/') || str_starts_with($path, 'shorts/')) {
                $id = explode('/', $path)[1] ?? null;
            }
        } elseif ($host === 'youtu.be') {
            $id = explode('/', $path)[0] ?? null;
        }

        return is_string($id) && preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? $id : null;
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
