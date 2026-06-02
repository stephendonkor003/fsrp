<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberStateCommodityTrend extends BaseModel
{
    use HasFactory;

    protected $table = 'myb_member_state_commodity_trends';

    protected $fillable = [
        'member_state_id',
        'commodity_id',
        'recorded_on',
        'production_volume',
        'stock_volume',
        'export_volume',
        'import_volume',
        'export_value_usd',
        'market_price',
        'market_price_currency',
        'growth_rate_pct',
        'availability_score',
        'trend_summary',
        'impact_notes',
        'review_status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'recorded_on' => 'date',
        'production_volume' => 'decimal:3',
        'stock_volume' => 'decimal:3',
        'export_volume' => 'decimal:3',
        'import_volume' => 'decimal:3',
        'export_value_usd' => 'decimal:2',
        'market_price' => 'decimal:2',
        'growth_rate_pct' => 'decimal:3',
        'availability_score' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function scopeApproved($query)
    {
        return $query->where('review_status', 'approved');
    }

    public function memberState(): BelongsTo
    {
        return $this->belongsTo(AuMemberState::class, 'member_state_id');
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class, 'commodity_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
