<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'question',
        'help_text',
        'category',
        'is_active',
        'sort_order',

    ];

    protected $casts = ['is_active' => 'boolean'];

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }
}
