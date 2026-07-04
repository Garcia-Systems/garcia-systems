<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrictionPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_id',
        'name',
        'slug',
        'description',
        'impact',

    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function solutionPatterns()
    {
        return $this->belongsToMany(SolutionPattern::class);
    }
}
