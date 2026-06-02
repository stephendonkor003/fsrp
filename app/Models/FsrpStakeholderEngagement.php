<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FsrpStakeholderEngagement extends BaseModel
{
    protected $table = 'fsrp_stakeholder_engagements';

    protected $fillable = [
        'engagement_code',
        'title',
        'fsrp_component_id',
        'fsrp_subcomponent_id',
        'engagement_date',
        'location',
        'stakeholder_group',
        'participants_count',
        'summary',
        'commitments_made',
        'follow_up_actions',
        'follow_up_due_on',
        'status',
        'created_by',
    ];

    protected $casts = [
        'engagement_date' => 'date',
        'follow_up_due_on' => 'date',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(FsrpComponent::class, 'fsrp_component_id');
    }

    public function subcomponent(): BelongsTo
    {
        return $this->belongsTo(FsrpSubcomponent::class, 'fsrp_subcomponent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function nextCode(): string
    {
        do {
            $code = 'SEP-' . now()->format('Y') . '-' . Str::upper(Str::random(5));
        } while (self::where('engagement_code', $code)->exists());

        return $code;
    }
}
