<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class MemberStateReportingCycle extends BaseModel
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_OPEN => 'Open',
        self::STATUS_CLOSED => 'Closed',
    ];

    public const FREQUENCY_QUARTERLY = 'QUARTERLY';

    public const FREQUENCY_SEMI_ANNUAL = 'SEMI_ANNUAL';

    public const FREQUENCY_ANNUAL = 'ANNUAL';

    public const FREQUENCY_METADATA = [
        self::FREQUENCY_QUARTERLY => [
            'label' => 'Quarterly',
            'periods_per_year' => 4,
            'months_per_period' => 3,
        ],
        self::FREQUENCY_SEMI_ANNUAL => [
            'label' => 'Semi-Annual',
            'periods_per_year' => 2,
            'months_per_period' => 6,
        ],
        self::FREQUENCY_ANNUAL => [
            'label' => 'Annual',
            'periods_per_year' => 1,
            'months_per_period' => 12,
        ],
    ];

    protected $table = 'me_member_state_reporting_cycles';

    protected $fillable = [
        'reporting_frequency_id',
        'period_key',
        'label',
        'reporting_year',
        'period_number',
        'period_start',
        'period_end',
        'opens_at',
        'closes_at',
        'status',
        'instructions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'reporting_year' => 'integer',
        'period_number' => 'integer',
        'period_start' => 'date',
        'period_end' => 'date',
        'opens_at' => 'datetime',
        'closes_at' => 'datetime',
    ];

    public function reportingFrequency(): BelongsTo
    {
        return $this->belongsTo(ReportingFrequency::class, 'reporting_frequency_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(MemberStateReportSubmission::class, 'reporting_cycle_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeAcceptingSubmissions(Builder $query, ?CarbonInterface $at = null): Builder
    {
        $at ??= now();

        return $query
            ->open()
            ->where(function (Builder $windowQuery) use ($at) {
                $windowQuery->whereNull('opens_at')->orWhere('opens_at', '<=', $at);
            })
            ->where(function (Builder $windowQuery) use ($at) {
                $windowQuery->whereNull('closes_at')->orWhere('closes_at', '>=', $at);
            });
    }

    public function scopeForFrequency(Builder $query, string $code): Builder
    {
        return $query->whereHas('reportingFrequency', function (Builder $frequencyQuery) use ($code) {
            $frequencyQuery->where('code', strtoupper(trim($code)));
        });
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('reporting_year', $year);
    }

    public function isAcceptingSubmissions(?CarbonInterface $at = null): bool
    {
        $at ??= now();

        if ($this->status !== self::STATUS_OPEN) {
            return false;
        }

        if ($this->opens_at && $this->opens_at->isAfter($at)) {
            return false;
        }

        return ! $this->closes_at || $this->closes_at->isAfter($at) || $this->closes_at->equalTo($at);
    }

    public function getDisplayLabelAttribute(): string
    {
        return $this->label
            ?: $this->period_key
            ?: trim(implode(' ', array_filter([$this->reporting_year, $this->period_number])));
    }

    public static function statusOptions(): array
    {
        return self::STATUSES;
    }

    public static function allowedFrequencyCodes(): array
    {
        return array_keys(self::FREQUENCY_METADATA);
    }

    public static function frequencyMetadata(string $code): ?array
    {
        return self::FREQUENCY_METADATA[strtoupper(trim($code))] ?? null;
    }

    /**
     * Build the canonical identity and dates for a configured reporting period.
     *
     * @return array{period_key: string, label: string, reporting_year: int, period_number: int, period_start: CarbonImmutable, period_end: CarbonImmutable}
     */
    public static function buildPeriodAttributes(string $frequencyCode, int $year, int $periodNumber = 1): array
    {
        $code = strtoupper(trim($frequencyCode));
        $metadata = self::frequencyMetadata($code);

        if (! $metadata) {
            throw new InvalidArgumentException("Unsupported Member State reporting frequency [{$frequencyCode}].");
        }

        if ($year < 1900 || $year > 9999) {
            throw new InvalidArgumentException('The reporting year must be between 1900 and 9999.');
        }

        if ($periodNumber < 1 || $periodNumber > $metadata['periods_per_year']) {
            throw new InvalidArgumentException(
                "Period number must be between 1 and {$metadata['periods_per_year']} for {$metadata['label']} reporting."
            );
        }

        $startMonth = (($periodNumber - 1) * $metadata['months_per_period']) + 1;
        $periodStart = CarbonImmutable::create($year, $startMonth, 1)->startOfDay();
        $periodEnd = $periodStart->addMonths($metadata['months_per_period'])->subDay()->endOfDay();

        [$periodKey, $label] = match ($code) {
            self::FREQUENCY_QUARTERLY => ["{$year}-Q{$periodNumber}", "Quarter {$periodNumber}, {$year}"],
            self::FREQUENCY_SEMI_ANNUAL => [
                "{$year}-H{$periodNumber}",
                ($periodNumber === 1 ? 'First Half' : 'Second Half').", {$year}",
            ],
            self::FREQUENCY_ANNUAL => ["{$year}-ANNUAL", "Annual, {$year}"],
        };

        return [
            'period_key' => $periodKey,
            'label' => $label,
            'reporting_year' => $year,
            'period_number' => $periodNumber,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ];
    }
}
