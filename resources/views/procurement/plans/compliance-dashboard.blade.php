@extends('layouts.app')

@section('content')
    <div class="nxl-container">
        <div class="page-header d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="page-title mb-1">Procurement Compliance Dashboard</h4>
                <p class="text-muted mb-0">STEP uploads, prior review, no-objection, risk, and annual plan update flags.</p>
            </div>
            <a href="{{ route('procurement.plans.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="feather-arrow-left me-1"></i> Back to Plans
            </a>
        </div>

        <div class="row g-3 mb-3">
            @foreach ([
                ['label' => 'Total Packages', 'value' => $dashboard['total'], 'class' => 'primary'],
                ['label' => 'STEP Pending', 'value' => $dashboard['step_pending'], 'class' => 'warning'],
                ['label' => 'Prior Review', 'value' => $dashboard['prior_review'], 'class' => 'info'],
                ['label' => 'No-Objection Pending', 'value' => $dashboard['no_objection_pending'], 'class' => 'secondary'],
                ['label' => 'High Risk', 'value' => $dashboard['high_risk'], 'class' => 'danger'],
                ['label' => 'Annual Update Due', 'value' => $dashboard['annual_update_due'], 'class' => 'dark'],
            ] as $card)
                <div class="col-md-4 col-xl-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="h3 mb-0 text-{{ $card['class'] }}">{{ number_format($card['value']) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Flagged Procurement Packages</h5>
            </div>
            <div class="card-body">
                <x-data-table id="procurementComplianceTable">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>FSRP</th>
                            <th>STEP</th>
                            <th>WB Review</th>
                            <th>Risk</th>
                            <th>Fiscal Year</th>
                            <th>Flags</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($flaggedPlans as $plan)
                            <tr>
                                <td class="fw-semibold text-primary">{{ $plan->procurement_code }}</td>
                                <td>
                                    <div class="fw-semibold">{{ Str::limit($plan->title, 45) }}</div>
                                    <small class="text-muted">{{ $plan->methodPlanned?->method_name ?: 'No method' }}</small>
                                </td>
                                <td>
                                    {{ $plan->fsrpComponent?->code ?: 'N/A' }}
                                    @if($plan->fsrpSubcomponent)
                                        <div class="small text-muted">{{ $plan->fsrpSubcomponent->code }}</div>
                                    @endif
                                </td>
                                <td>
                                    {{ ucwords(str_replace('_', ' ', $plan->step_plan_status ?: 'not_uploaded')) }}
                                    @if($plan->step_plan_id)
                                        <div class="small text-muted">{{ $plan->step_plan_id }}</div>
                                    @endif
                                </td>
                                <td>{{ ucwords(str_replace('_', ' ', $plan->world_bank_no_objection_status ?: 'pending')) }}</td>
                                <td>{{ $plan->procurement_risk_level ? ucfirst($plan->procurement_risk_level) : 'N/A' }}</td>
                                <td>{{ $plan->fiscal_year ?: $currentYear }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($plan->compliance_flags as $flag)
                                            <span class="badge bg-light text-dark">{{ $flag }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('procurement.plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="feather-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-data-table>
            </div>
        </div>
    </div>
@endsection
