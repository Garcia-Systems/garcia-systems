<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrictionPoint extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean', 'answers' => 'array'];
    }

    public function workflow(){return $this->belongsTo(Workflow::class);} public function solutionPatterns(){return $this->belongsToMany(SolutionPattern::class);} 
}
