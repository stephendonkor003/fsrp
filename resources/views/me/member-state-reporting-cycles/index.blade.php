@extends('layouts.app')

@section('title', 'Member State Reporting Cycles')

@section('content')
    <div class="nxl-container">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="feather-calendar text-primary me-2"></i>
                    Member State Reporting Cycles
                </h4>
                <p class="text-muted mb-0">Open Quarterly, Semi-Annual, or Annual reporting periods for Member States.</p>
            </div>
            @can('me.configuration.manage')
                <a href="{{ route('budget.me.member-state-reporting-cycles.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-1"></i> Configure reporting cycle
                </a>
            @endcan
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="alert alert-info border-0 shadow-sm d-flex gap-3 align-items-start mb-4">
            <i class="feather-shield fs-4 mt-1"></i>
            <div>
                <strong>Duplicate protection is automatic.</strong>
                <div class="small mt-1">
                    Each country receives one reporting workspace per configured cycle. Reopening the same period continues
                    the existing report instead of creating a second submission.
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Configured reporting cycles</strong>
                <span class="badge bg-light text-dark">{{ $cycles->total() }} total</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Frequency</th>
                            <th>Reporting period</th>
                            <th>Period dates</th>
                            <th>Submission window</th>
                            <th>Status</th>
                            <th>Country reports</th>
                            @can('me.configuration.manage')
                                <th class="text-end pe-4">Actions</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cycles as $cycle)
                            @php
                                $statusClass = match ($cycle->status) {
                                    \App\Models\MemberStateReportingCycle::STATUS_OPEN => 'bg-success',
                                    \App\Models\MemberStateReportingCycle::STATUS_CLOSED => 'bg-secondary',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ $cycle->reportingFrequency?->name ?? 'Unavailable' }}</div>
                                    <code class="small">{{ $cycle->reportingFrequency?->code }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $cycle->display_label }}</div>
                                    <div class="small text-muted">{{ $cycle->period_key }}</div>
                                </td>
                                <td class="small">
                                    {{ $cycle->period_start?->format('d M Y') }}
                                    <span class="text-muted mx-1">to</span>
                                    {{ $cycle->period_end?->format('d M Y') }}
                                </td>
                                <td class="small">
                                    <div>Opens: {{ $cycle->opens_at?->format('d M Y H:i') ?? 'Immediately' }}</div>
                                    <div>Closes: {{ $cycle->closes_at?->format('d M Y H:i') ?? 'No closing date' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $statusClass }}">{{ \App\Models\MemberStateReportingCycle::STATUSES[$cycle->status] ?? ucfirst($cycle->status) }}</span>
                                    @if ($cycle->isAcceptingSubmissions())
                                        <div class="small text-success mt-1"><i class="feather-check-circle me-1"></i>Accepting reports</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $cycle->submissions_count }}</span>
                                </td>
                                @can('me.configuration.manage')
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('budget.me.member-state-reporting-cycles.edit', $cycle) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit cycle">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('budget.me.member-state-reporting-cycles.destroy', $cycle) }}"
                                                onsubmit="return confirm('Delete this reporting cycle?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Delete cycle">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="feather-calendar d-block fs-2 text-muted mb-2"></i>
                                    <strong>No reporting cycles configured</strong>
                                    <div class="small text-muted mt-1">Create a cycle to make a frequency available in the Member State portal.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($cycles->hasPages())
                <div class="card-footer bg-white">{{ $cycles->links() }}</div>
            @endif
        </div>
    </div>
@endsection
