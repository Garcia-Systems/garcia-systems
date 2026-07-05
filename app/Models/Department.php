<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_type_id',
        'name',
        'slug',
        'description',

    ];

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class);
    }

    public function workflows()
    {
        return $this->hasMany(Workflow::class);
    }
}
