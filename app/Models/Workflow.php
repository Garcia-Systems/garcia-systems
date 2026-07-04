<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry_id',
        'company_type_id',
        'department_id',
        'name',
        'slug',
        'description',

    ];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function companyType()
    {
        return $this->belongsTo(CompanyType::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function frictionPoints()
    {
        return $this->hasMany(FrictionPoint::class);
    }
}
