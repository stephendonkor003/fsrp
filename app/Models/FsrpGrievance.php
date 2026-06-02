<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FsrpGrievance extends BaseModel
{
    protected $table = 'fsrp_grievances';

    protected $fillable = [
        'case_code',
        'complainant_name',
        'complainant_contact',
        'category',
        'priority',
        'status',
        'fsrp_component_id',
        'fsrp_subcomponent_id',
        'received_on',
        'description',
        'resolution_actions',
        'assigned_to',
        'due_on',
        'closed_at',
        'closure_notes',
        'created_by',
    ];

    protected $casts = [
        'received_on' => 'date',
        'due_on' => 'date',
        'closed_at' => 'datetime',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(FsrpComponent::class, 'fsrp_component_id');
    }

    public function subcomponent(): BelongsTo
    {
        return $this->belongsTo(FsrpSubcomponent::class, 'fsrp_subcomponent_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function nextCode(): string
    {
        do {
            $code = 'GRM-' . now()->format('Y') . '-' . Str::upper(Str::random(5));
        } while (self::where('case_code', $code)->exists());

        return $code;
    }
}
