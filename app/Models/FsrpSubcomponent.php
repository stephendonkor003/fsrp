<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FsrpSubcomponent extends BaseModel
{
    protected $table = 'fsrp_subcomponents';

    protected $fillable = [
        'component_id',
        'code',
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(FsrpComponent::class, 'component_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
