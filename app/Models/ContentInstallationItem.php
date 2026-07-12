<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentInstallationItem extends Model
{
    protected $fillable = ['content_installation_run_id', 'item_type', 'model_type', 'model_id', 'stable_key', 'action', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(ContentInstallationRun::class, 'content_installation_run_id');
    }
}
