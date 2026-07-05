<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'company',
        'score',
        'result_tier',
        'summary',
        'category_scores',
        'risks',
        'next_steps',
        'recommendations',
        'service_cta',
        'lead_id',

    ];

    protected $casts = [
        'category_scores' => 'array',
        'risks' => 'array',
        'next_steps' => 'array',
        'recommendations' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
