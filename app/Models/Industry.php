<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',

    ];

    public function companyTypes()
    {
        return $this->hasMany(CompanyType::class);
    }

    public function workflows()
    {
        return $this->hasMany(Workflow::class);
    }
}
