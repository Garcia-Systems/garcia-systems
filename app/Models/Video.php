<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean', 'answers' => 'array'];
    }

    
}
