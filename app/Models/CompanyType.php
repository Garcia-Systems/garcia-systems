<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
    use HasFactory;

    protected $fillable = [
        'industry_id',
        'name',
        'slug',
        'description',
        'managed_content_key',

    ];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function workflows()
    {
        return $this->hasMany(Workflow::class);
    }
}
