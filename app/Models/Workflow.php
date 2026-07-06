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
        'assessment_path',

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

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }
}
