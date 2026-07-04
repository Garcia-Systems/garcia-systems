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

    ];

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }
}
