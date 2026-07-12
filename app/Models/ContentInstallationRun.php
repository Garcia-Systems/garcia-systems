<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentInstallationRun extends Model
{
    protected $fillable = ['uuid', 'dataset', 'status', 'started_at', 'completed_at', 'rolled_back_at', 'executed_by', 'metadata'];

    protected $casts = ['started_at' => 'datetime', 'completed_at' => 'datetime', 'rolled_back_at' => 'datetime', 'metadata' => 'array'];

    public function items(): HasMany
    {
        return $this->hasMany(ContentInstallationItem::class);
    }
}
