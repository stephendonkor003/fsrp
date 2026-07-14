<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberStateReportSubmission extends BaseModel
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_REVISION_REQUIRED = 'revision_required';

    public const STATUS_APPROVED = 'approved';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_SUBMITTED => 'Submitted',
        self::STATUS_REVISION_REQUIRED => 'Revision Required',
        self::STATUS_APPROVED => 'Approved',
    ];

    protected $table = 'myb_member_state_report_submissions';

    protected $fillable = [
        'member_state_id',
        'reporting_cycle_id',
        'status',
        'started_by',
        'submitted_by',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function memberState(): BelongsTo
    {
        return $this->belongsTo(AuMemberState::class, 'member_state_id');
    }

    public function reportingCycle(): BelongsTo
    {
        return $this->belongsTo(MemberStateReportingCycle::class, 'reporting_cycle_id');
    }

    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function scopeForMemberState(Builder $query, AuMemberState|string $memberState): Builder
    {
        $memberStateId = $memberState instanceof AuMemberState ? $memberState->getKey() : $memberState;

        return $query->where('member_state_id', $memberStateId);
    }

    public function scopeForCycle(Builder $query, MemberStateReportingCycle|string $cycle): Builder
    {
        $cycleId = $cycle instanceof MemberStateReportingCycle ? $cycle->getKey() : $cycle;

        return $query->where('reporting_cycle_id', $cycleId);
    }

    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->withStatus(self::STATUS_DRAFT);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->withStatus(self::STATUS_SUBMITTED);
    }

    public function scopeEditable(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_DRAFT, self::STATUS_REVISION_REQUIRED]);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVISION_REQUIRED], true);
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_APPROVED], true);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? str($this->status)->replace('_', ' ')->title()->toString();
    }

    public static function statusOptions(): array
    {
        return self::STATUSES;
    }
}
