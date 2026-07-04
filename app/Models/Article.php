<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean', 'answers' => 'array'];
    }

    public function category(){return $this->belongsTo(Category::class);} public function tags(){return $this->belongsToMany(Tag::class);} 
}
