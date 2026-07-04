<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean', 'answers' => 'array'];
    }

    public function industry(){return $this->belongsTo(Industry::class);} public function department(){return $this->belongsTo(Department::class);} public function frictionPoints(){return $this->hasMany(FrictionPoint::class);} 
}
