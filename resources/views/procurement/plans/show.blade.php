@extends('layouts.app')

@section('content')
    <div class="nxl-container">

        {{-- ================= PAGE HEADER ================= --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="page-title mb-1">Procurement Plan Details</h4>
                <p class="text-muted mb-0">
                    <span class="badge bg-primary px-3 py-1">{{ $plan->procurement_code }}</span>
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('procurement.plans.edit', $plan) }}" class="btn btn-warning btn-sm">
                    <i class="feather-edit me-1"></i> Edit Plan
                </a>
                <a href="{{ route('procurement.plans.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="feather-arrow-left me-1"></i> Back to Plans
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Main Information --}}
            <div class="col-lg-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Plan Information</h5>
                        @if($plan->is_launched)
                            <span class="badge bg-success px-3 py-2">Launched</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Not Launched</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <h5 class="fw-bold text-dark">{{ $plan->title }}</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small">Procurement Code</label>
                                <p class="mb-0 fw-semibold text-primary">{{ $plan->procurement_code }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small">Estimated Budget</label>
                                <p class="mb-0 fw-semibold">${{ number_format($plan->estimated_budget ?? 0, 2) }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small">Activity</label>
                                <p class="mb-0">
                                    @if($plan->activity)
                                        <span class="badge bg-light text-dark">{{ $plan->activity->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small">Sub Activity</label>
                                <p class="mb-0">
                                    @if($plan->subActivity)
                                        <span class="badge bg-light text-dark">{{ $plan->subActivity->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small">Procurement Method</label>
                                <p class="mb-0">
                                    @if($plan->methodPlanned)
                                        <span class="badge bg-info">{{ $plan->methodPlanned->method_name }}</span>
                                        <small class="text-muted">({{ $plan->methodPlanned->method_target_days }} days)</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small">Geographic Location</label>
                                <p class="mb-0">
                                    @if($plan->geographic)
                                        {{ $plan->geographic->name }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small">FSRP Component</label>
                                <p class="mb-0">
                                    @if($plan->fsrpComponent)
                                        {{ $plan->fsrpComponent->code }} - {{ $plan->fsrpComponent->name }}
                                    @else
                                        <span class="text-muted">â€”</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small">FSRP Subcomponent</label>
                                <p class="mb-0">
                                    @if($plan->fsrpSubcomponent)
                                        {{ $plan->fsrpSubcomponent->code }} - {{ $plan->fsrpSubcomponent->name }}
                                    @else
                                        <span class="text-muted">â€”</span>
                                    @endif
                                </p>
                            </div>

                            @if($plan->description)
                                <div class="col-12">
                                    <label class="text-muted small">Description</label>
                                    <p class="mb-0">{{ $plan->description }}</p>
                                </div>
                            @endif

                            @if($plan->remarks)
                                <div class="col-12">
                                    <label class="text-muted small">Notes</label>
                                    <p class="mb-0">{{ $plan->remarks }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">World Bank Procurement Tracking</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small">PPSD Reference</label>
                                <p class="mb-0">{{ $plan->ppsd_reference ?: 'Not recorded' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">STEP Plan ID</label>
                                <p class="mb-0">{{ $plan->step_plan_id ?: 'Not recorded' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">STEP Status</label>
                                <p class="mb-0">{{ ucwords(str_replace('_', ' ', $plan->step_plan_status ?: 'not_uploaded')) }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">STEP Upload Date</label>
                                <p class="mb-0">{{ $plan->step_last_uploaded_at?->format('F d, Y') ?? 'Not recorded' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Prior Review</label>
                                <p class="mb-0">{{ $plan->prior_review_required ? 'Required' : 'Not required' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">WB No-Objection Status</label>
                                <p class="mb-0">{{ ucwords(str_replace('_', ' ', $plan->world_bank_no_objection_status ?: 'pending')) }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">WB No-Objection Date</label>
                                <p class="mb-0">{{ $plan->world_bank_no_objection_date?->format('F d, Y') ?? 'Not recorded' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small">Risk Level</label>
                                <p class="mb-0">{{ $plan->procurement_risk_level ? ucwords($plan->procurement_risk_level) : 'Not recorded' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Contract Log Reference</label>
                                <p class="mb-0">{{ $plan->contract_log_reference ?: 'Not recorded' }}</p>
                            </div>
                            @if($plan->procurement_record_notes)
                                <div class="col-md-6">
                                    <label class="text-muted small">Record Notes</label>
                                    <p class="mb-0">{{ $plan->procurement_record_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light">
                                    <label class="text-muted small">Estimated Start Date</label>
                                    <p class="mb-0 fw-semibold">
                                        @if($plan->estimated_start_date)
                                            <i class="feather-calendar text-primary me-1"></i>
                                            {{ $plan->estimated_start_date->format('F d, Y') }}
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light">
                                    <label class="text-muted small">Estimated End Date</label>
                                    <p class="mb-0 fw-semibold">
                                        @if($plan->estimated_end_date)
                                            <i class="feather-calendar text-danger me-1"></i>
                                            {{ $plan->estimated_end_date->format('F d, Y') }}
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($plan->estimated_start_date && $plan->estimated_end_date)
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <label class="text-muted small">Duration</label>
                                        <p class="mb-0 fw-semibold">
                                            <i class="feather-clock text-info me-1"></i>
                                            {{ $plan->estimated_start_date->diffInDays($plan->estimated_end_date) }} days
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Info --}}
            <div class="col-lg-4">
                {{-- Stage & Status --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Stage & Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Procurement Stage</label>
                            <p class="mb-0">
                                @if($plan->stage)
                                    <span class="badge bg-secondary px-3 py-1">{{ $plan->stage->stage_name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Procurement Status</label>
                            <p class="mb-0">
                                @if($plan->status)
                                    <span class="badge px-3 py-1"
                                        style="background-color: {{ $plan->status->color ?? '#6c757d' }}; color: white;">
                                        {{ $plan->status->name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Step Stage</label>
                            <p class="mb-0">
                                @if($plan->stepStage)
                                    <span class="badge bg-info px-3 py-1">{{ $plan->stepStage->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-muted small">Step Approval Process</label>
                            <p class="mb-0">
                                @if($plan->stepApproval)
                                    <span class="badge bg-warning text-dark px-3 py-1">{{ $plan->stepApproval->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="{{ route('procurement.plans.toggle-launch', $plan) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                @if($plan->is_launched)
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('Are you sure you want to mark this plan as not launched?')">
                                        <i class="feather-x-circle me-1"></i> Mark as Not Launched
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Are you sure you want to launch this plan?')">
                                        <i class="feather-check-circle me-1"></i> Launch Plan
                                    </button>
                                @endif
                            </form>
                            <a href="{{ route('procurement.plans.edit', $plan) }}" class="btn btn-outline-warning">
                                <i class="feather-edit me-1"></i> Edit Plan
                            </a>
                            <form action="{{ route('procurement.plans.destroy', $plan) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this procurement plan?')">
                                    <i class="feather-trash-2 me-1"></i> Delete Plan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Meta Info --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Meta Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Created By</label>
                            <p class="mb-0">{{ $plan->creator->name ?? '—' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Created At</label>
                            <p class="mb-0">{{ $plan->created_at->format('F d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="text-muted small">Last Updated</label>
                            <p class="mb-0">{{ $plan->updated_at->format('F d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
