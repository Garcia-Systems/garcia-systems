<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'help_text',
        'category',
        'sort_order',
        'weight',
        'is_active',

    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }
}
