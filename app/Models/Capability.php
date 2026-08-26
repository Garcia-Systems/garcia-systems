<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capability extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'managed_content_key',

    ];

    public function solutionPatterns()
    {
        return $this->belongsToMany(SolutionPattern::class);
    }
}
