<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class FsrpComponent extends BaseModel
{
    protected $table = 'fsrp_components';

    protected $fillable = [
        'code',
        'name',
        'description',
        'auc_allocation_usd',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'auc_allocation_usd' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function subcomponents(): HasMany
    {
        return $this->hasMany(FsrpSubcomponent::class, 'component_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
