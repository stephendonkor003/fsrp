@extends('layouts.app')

@section('title', 'Food Commodity Review Desk')

@push('styles')
<style>
.ctr-hero{border-radius:16px;padding:1.1rem 1.2rem;background:linear-gradient(128deg,#0f172a 0%,#166534 50%,#d97706 100%);color:#f8fafc;box-shadow:0 14px 28px rgba(15,23,42,.18)}
.ctr-hero .kicker{font-size:.72rem;letter-spacing:.08em;text-transform:uppercase;color:rgba(248,250,252,.82)}
.ctr-hero h4{margin:.2rem 0 .35rem;color:#fff;font-weight:800}
.ctr-stat{border:1px solid #dbeafe;border-radius:12px;background:#fff;padding:.75rem .85rem;box-shadow:0 5px 12px rgba(15,23,42,.06)}
.ctr-stat .label{font-size:.7rem;letter-spacing:.06em;text-transform:uppercase;color:#64748b}
.ctr-stat .value{font-size:1.16rem;font-weight:800;color:#0f172a}
.ctr-panel{border:1px solid #dbeafe;border-radius:14px;background:#fff;box-shadow:0 8px 18px rgba(15,23,42,.07)}
.ctr-panel .card-header{border-bottom:1px solid #e2e8f0;background:linear-gradient(120deg,#f8fafc 0%,#fefce8 100%)}
.ctr-filter{border:1px solid #dbeafe;border-radius:12px;background:#f8fbff;padding:.72rem}
.ctr-item{border:1px solid #e2e8f0;border-radius:12px;background:#fff;padding:.9rem;box-shadow:0 4px 12px rgba(15,23,42,.05)}
.ctr-item + .ctr-item{margin-top:.78rem}
.ctr-order{width:35px;height:35px;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;color:#fff;background:linear-gradient(140deg,#15803d 0%,#d97706 100%);box-shadow:0 8px 14px rgba(15,23,42,.16)}
.ctr-badge{border-radius:999px;padding:.22rem .58rem;font-size:.72rem;font-weight:700;display:inline-flex;align-items:center;gap:.3rem}
.ctr-review-pending{background:#fef3c7;color:#92400e}
.ctr-review-approved{background:#dcfce7;color:#166534}
.ctr-review-revision_required{background:#fee2e2;color:#991b1b}
.ctr-review-rejected{background:#e2e8f0;color:#334155}
.ctr-meta{font-size:.79rem;color:#64748b}
.ctr-summary{font-size:.88rem;color:#334155}
.ctr-metric{border:1px solid #dbeafe;border-radius:10px;background:#f8fbff;padding:.48rem .58rem}
.ctr-metric .k{font-size:.7rem;color:#64748b;text-transform:uppercase;letter-spacing:.06em}
.ctr-metric .v{font-weight:700;color:#0f172a}
.ctr-form{border:1px solid #dbeafe;border-radius:12px;background:#f8fafc;padding:.72rem}
.ctr-empty{border:1px dashed #cbd5e1;border-radius:12px;background:#f8fafc;padding:1rem;text-align:center;color:#64748b}
</style>
@endpush

@section('content')
@php
    $reviewLabels = [
        'pending' => 'Pending Review',
        'approved' => 'Approved',
        'revision_required' => 'Revision Required',
        'rejected' => 'Rejected',
    ];
@endphp
<main class="nxl-container">
    <div class="ctr-hero mb-4">
        <div class="kicker">Back-Office Validation</div>
        <h4>Food Commodity Review Desk</h4>
        <p class="mb-0">Approve member-state commodity submissions before they feed the public food commodities map and comparison dashboards.</p>
    </div>

    @if (session('success')) <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger border-0 shadow-sm">Please correct the review form validation errors and submit again.</div> @endif

    <div class="row g-3 mb-4">
        <div class="col-md-2"><div class="ctr-stat"><div class="label">Total</div><div class="value">{{ number_format((int) ($stats['total'] ?? 0)) }}</div></div></div>
        <div class="col-md-2"><div class="ctr-stat"><div class="label">Pending</div><div class="value text-warning">{{ number_format((int) ($stats['pending'] ?? 0)) }}</div></div></div>
        <div class="col-md-2"><div class="ctr-stat"><div class="label">Approved</div><div class="value text-success">{{ number_format((int) ($stats['approved'] ?? 0)) }}</div></div></div>
        <div class="col-md-2"><div class="ctr-stat"><div class="label">Revision Req.</div><div class="value text-danger">{{ number_format((int) ($stats['revision_required'] ?? 0)) }}</div></div></div>
        <div class="col-md-2"><div class="ctr-stat"><div class="label">Rejected</div><div class="value text-secondary">{{ number_format((int) ($stats['rejected'] ?? 0)) }}</div></div></div>
        <div class="col-md-2"><div class="ctr-stat"><div class="label">Approved Availability</div><div class="value text-primary">{{ number_format((float) ($stats['approved_avg_availability'] ?? 0), 1) }}%</div></div></div>
    </div>

    <div class="card ctr-panel">
        <div class="card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0 fw-bold text-dark"><i class="feather-check-circle me-1"></i> Commodity Review Queue</h5>
                <form method="GET" action="{{ route('system.commodity-trend-reviews.index') }}" class="ctr-filter d-flex flex-wrap gap-2">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control form-control-sm" style="min-width:210px;" placeholder="Search state, commodity, notes...">
                    <select name="member_state_id" class="form-select form-select-sm" style="min-width:170px;">
                        <option value="">All member states</option>
                        @foreach($memberStates as $state)
                            <option value="{{ $state->id }}" @selected(($filters['member_state_id'] ?? '') === $state->id)>{{ $state->name }}</option>
                        @endforeach
                    </select>
                    <select name="commodity_id" class="form-select form-select-sm" style="min-width:170px;">
                        <option value="">All commodities</option>
                        @foreach($commodities as $commodity)
                            <option value="{{ $commodity->id }}" @selected(($filters['commodity_id'] ?? '') === $commodity->id)>{{ $commodity->name }}</option>
                        @endforeach
                    </select>
                    <select name="review_status" class="form-select form-select-sm" style="min-width:160px;">
                        <option value="">All review status</option>
                        @foreach($reviewLabels as $key => $label)
                            <option value="{{ $key }}" @selected(($filters['review_status'] ?? '') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control form-control-sm">
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control form-control-sm">
                    <button class="btn btn-sm btn-outline-secondary">Filter</button>
                    <a href="{{ route('system.commodity-trend-reviews.index') }}" class="btn btn-sm btn-light border">Clear</a>
                </form>
            </div>
        </div>
        <div class="card-body">
            @forelse($entries as $entry)
                @php
                    $rowNumber = ((int) ($entries->firstItem() ?? 1)) + $loop->index;
                    $reviewStatus = (string) ($entry->review_status ?? 'pending');
                    $reviewLabel = $reviewLabels[$reviewStatus] ?? ucfirst(str_replace('_', ' ', $reviewStatus));
                @endphp
                <article class="ctr-item">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="ctr-order">{{ $rowNumber }}</span>
                            <div>
                                <h6 class="mb-1 text-dark">{{ $entry->commodity?->name ?? 'Unknown commodity' }}</h6>
                                <div class="ctr-meta">
                                    <strong>{{ $entry->memberState?->name ?? 'N/A' }}</strong> |
                                    {{ optional($entry->recorded_on)->format('d M Y') }} |
                                    {{ $entry->commodity?->category ?: 'Food commodity' }}
                                </div>
                            </div>
                        </div>
                        <span class="ctr-badge ctr-review-{{ $reviewStatus }}">{{ $reviewLabel }}</span>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-md-2"><div class="ctr-metric"><div class="k">Production</div><div class="v">{{ $entry->production_volume !== null ? number_format((float) $entry->production_volume, 3) : '-' }}</div></div></div>
                        <div class="col-md-2"><div class="ctr-metric"><div class="k">Stock</div><div class="v">{{ $entry->stock_volume !== null ? number_format((float) $entry->stock_volume, 3) : '-' }}</div></div></div>
                        <div class="col-md-2"><div class="ctr-metric"><div class="k">Export</div><div class="v">{{ $entry->export_volume !== null ? number_format((float) $entry->export_volume, 3) : '-' }}</div></div></div>
                        <div class="col-md-2"><div class="ctr-metric"><div class="k">Import</div><div class="v">{{ $entry->import_volume !== null ? number_format((float) $entry->import_volume, 3) : '-' }}</div></div></div>
                        <div class="col-md-2"><div class="ctr-metric"><div class="k">Availability</div><div class="v">{{ $entry->availability_score !== null ? number_format((float) $entry->availability_score, 1) . '%' : '-' }}</div></div></div>
                        <div class="col-md-2"><div class="ctr-metric"><div class="k">Market Price</div><div class="v">{{ $entry->market_price !== null ? number_format((float) $entry->market_price, 2) . ' ' . ($entry->market_price_currency ?: '') : '-' }}</div></div></div>
                    </div>

                    <div class="ctr-summary mt-2">{{ \Illuminate\Support\Str::limit((string) ($entry->trend_summary ?: $entry->impact_notes ?: 'No summary provided.'), 420) }}</div>

                    <details class="mt-2">
                        <summary class="small fw-semibold text-primary" style="cursor:pointer;">Open full commodity submission detail</summary>
                        <div class="row g-2 mt-2">
                            <div class="col-md-6"><strong>Trend summary:</strong><div class="small text-muted">{{ $entry->trend_summary ?: 'Not provided.' }}</div></div>
                            <div class="col-md-6"><strong>Impact notes:</strong><div class="small text-muted">{{ $entry->impact_notes ?: 'Not provided.' }}</div></div>
                            <div class="col-md-6"><strong>Growth rate:</strong><div class="small text-muted">{{ $entry->growth_rate_pct !== null ? number_format((float) $entry->growth_rate_pct, 3) . '%' : 'Not provided.' }}</div></div>
                            <div class="col-md-6"><strong>Export value:</strong><div class="small text-muted">{{ $entry->export_value_usd !== null ? 'USD ' . number_format((float) $entry->export_value_usd, 2) : 'Not provided.' }}</div></div>
                            <div class="col-md-6"><strong>Reviewer:</strong><div class="small text-muted">{{ $entry->reviewer?->name ?: 'Not reviewed yet' }}</div></div>
                            <div class="col-md-6"><strong>Reviewed at:</strong><div class="small text-muted">{{ $entry->reviewed_at ? $entry->reviewed_at->format('d M Y H:i') : 'Not reviewed yet' }}</div></div>
                            <div class="col-12"><strong>Review notes:</strong><div class="small text-muted">{{ $entry->review_notes ?: 'No review notes recorded.' }}</div></div>
                        </div>
                    </details>

                    @can('commodity_data.approve')
                        <form action="{{ route('system.commodity-trend-reviews.status.update', $entry) }}" method="POST" class="ctr-form mt-2">
                            @csrf
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Review decision</label>
                                    <select name="review_status" class="form-select form-select-sm">
                                        <option value="pending" @selected($entry->review_status === 'pending')>Pending Review</option>
                                        <option value="approved" @selected($entry->review_status === 'approved')>Approve for Public Map</option>
                                        <option value="revision_required" @selected($entry->review_status === 'revision_required')>Request Revision</option>
                                        <option value="rejected" @selected($entry->review_status === 'rejected')>Reject</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label small text-muted mb-1">Review notes / guidance to member state</label>
                                    <textarea name="review_notes" rows="2" class="form-control form-control-sm" placeholder="Provide clear feedback and next action...">{{ old('review_notes', $entry->review_notes) }}</textarea>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button class="btn btn-sm btn-success"><i class="feather-save me-1"></i>Save</button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="small text-muted mt-2">You can view submissions but do not have permission to change review status.</div>
                    @endcan
                </article>
            @empty
                <div class="ctr-empty">No commodity trend submissions found for the current filters.</div>
            @endforelse
        </div>
        @if($entries->hasPages())
            <div class="card-footer bg-white">{{ $entries->links() }}</div>
        @endif
    </div>
</main>
@endsection
