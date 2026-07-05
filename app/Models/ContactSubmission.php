<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'company',
        'service_interest',
        'message',
        'lead_id',

    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
