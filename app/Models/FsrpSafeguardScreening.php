<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class FsrpSafeguardScreening extends BaseModel
{
    protected $table = 'fsrp_safeguard_screenings';

    protected $fillable = [
        'screening_code',
        'title',
        'fsrp_component_id',
        'fsrp_subcomponent_id',
        'activity_id',
        'sub_activity_id',
        'procurement_plan_id',
        'approved_work_plan_id',
        'risk_level',
        'screening_status',
        'screened_on',
        'screened_by',
        'environmental_risks',
        'social_risks',
        'mitigation_measures',
        'evidence_reference',
        'next_review_due_on',
        'created_by',
    ];

    protected $casts = [
        'screened_on' => 'date',
        'next_review_due_on' => 'date',
    ];

    public function component(): BelongsTo
    {
        return $this->belongsTo(FsrpComponent::class, 'fsrp_component_id');
    }

    public function subcomponent(): BelongsTo
    {
        return $this->belongsTo(FsrpSubcomponent::class, 'fsrp_subcomponent_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function subActivity(): BelongsTo
    {
        return $this->belongsTo(SubActivity::class, 'sub_activity_id');
    }

    public function procurementPlan(): BelongsTo
    {
        return $this->belongsTo(ProcurementPlan::class, 'procurement_plan_id');
    }

    public function workPlan(): BelongsTo
    {
        return $this->belongsTo(ApprovedWorkPlan::class, 'approved_work_plan_id');
    }

    public function screener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }

    public static function nextCode(): string
    {
        do {
            $code = 'E&S-' . now()->format('Y') . '-' . Str::upper(Str::random(5));
        } while (self::where('screening_code', $code)->exists());

        return $code;
    }
}
