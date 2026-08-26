<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolutionPattern extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'managed_content_key',

    ];

    public function capabilities()
    {
        return $this->belongsToMany(Capability::class);
    }

    public function frictionPoints()
    {
        return $this->belongsToMany(FrictionPoint::class);
    }
}
