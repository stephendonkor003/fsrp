@extends('layouts.app')

@section('content')
    <div class="nxl-container">

        {{-- ================= PAGE HEADER ================= --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="page-title mb-1">Edit Procurement Plan</h4>
                <p class="text-muted mb-0">
                    Update procurement plan: <strong>{{ $plan->procurement_code }}</strong>
                </p>
            </div>

            <a href="{{ route('procurement.plans.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="feather-arrow-left me-1"></i> Back to Plans
            </a>
        </div>

        {{-- ================= FORM ================= --}}
        <form action="{{ route('procurement.plans.update', $plan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Plan Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Procurement Code --}}
                        <div class="col-md-6">
                            <label for="procurement_code" class="form-label">Procurement Code <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('procurement_code') is-invalid @enderror"
                                    id="procurement_code" name="procurement_code"
                                    value="{{ old('procurement_code', $plan->procurement_code) }}"
                                    placeholder="ET-AUC-XXXXXX-CS-CQS" required>
                                <button class="btn btn-outline-primary" type="button" id="generateCodeBtn">
                                    <i class="feather-refresh-cw me-1"></i> Regenerate
                                </button>
                            </div>
                            <small class="text-muted">Format: ET-AUC-XXXXXX-CS-CQS</small>
                            @error('procurement_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Title --}}
                        <div class="col-md-6">
                            <label for="title" class="form-label">Procurement Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title', $plan->title) }}"
                                placeholder="Enter procurement title" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Activity --}}
                        <div class="col-md-6">
                            <label for="activity_id" class="form-label">Activity <span class="text-danger">*</span></label>
                            <select class="form-select @error('activity_id') is-invalid @enderror"
                                id="activity_id" name="activity_id" required>
                                <option value="">Select Activity</option>
                                @foreach ($activities as $activity)
                                    <option value="{{ $activity->id }}"
                                        {{ old('activity_id', $plan->activity_id) == $activity->id ? 'selected' : '' }}>
                                        {{ $activity->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('activity_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Sub Activity --}}
                        <div class="col-md-6">
                            <label for="sub_activity_id" class="form-label">Sub Activity</label>
                            <select class="form-select @error('sub_activity_id') is-invalid @enderror"
                                id="sub_activity_id" name="sub_activity_id">
                                <option value="">Select Sub Activity</option>
                                @foreach ($subActivities as $subActivity)
                                    <option value="{{ $subActivity->id }}"
                                        {{ old('sub_activity_id', $plan->sub_activity_id) == $subActivity->id ? 'selected' : '' }}>
                                        {{ $subActivity->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sub_activity_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Method Planned --}}
                        <div class="col-md-6">
                            <label for="method_planned_id" class="form-label">Procurement Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('method_planned_id') is-invalid @enderror"
                                id="method_planned_id" name="method_planned_id" required>
                                <option value="">Select Method</option>
                                @foreach ($methods as $method)
                                    <option value="{{ $method->id }}"
                                        data-target-days="{{ $method->method_target_days }}"
                                        {{ old('method_planned_id', $plan->method_planned_id) == $method->id ? 'selected' : '' }}>
                                        {{ $method->method_name }} ({{ $method->method_target_days }} days)
                                    </option>
                                @endforeach
                            </select>
                            @error('method_planned_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Geographic --}}
                        <div class="col-md-6">
                            <label for="geographic_id" class="form-label">Geographic Location</label>
                            <select class="form-select @error('geographic_id') is-invalid @enderror"
                                id="geographic_id" name="geographic_id">
                                <option value="">Select Geographic</option>
                                @foreach ($geographics as $geographic)
                                    <option value="{{ $geographic->id }}"
                                        {{ old('geographic_id', $plan->geographic_id) == $geographic->id ? 'selected' : '' }}>
                                        {{ $geographic->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('geographic_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Program Plan --}}
                        <div class="col-md-6">
                            <label for="program_plan_id" class="form-label">Procurement Plan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('program_plan_id') is-invalid @enderror" id="program_plan_id"
                                name="program_plan_id" required>
                                <option value="">Select procurement plan</option>
                                @foreach ($programPlans as $programPlan)
                                    <option value="{{ $programPlan->id }}"
                                        {{ old('program_plan_id', $plan->program_plan_id) == $programPlan->id ? 'selected' : '' }}>
                                        {{ $programPlan->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('program_plan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Is Launched --}}
                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-semibold mb-0">FSRP Component Classification</h6>
                            <small class="text-muted">Links this procurement package to the FSRP operational taxonomy.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="fsrp_component_id" class="form-label">FSRP Component</label>
                            <select class="form-select @error('fsrp_component_id') is-invalid @enderror"
                                id="fsrp_component_id" name="fsrp_component_id">
                                <option value="">Select Component</option>
                                @foreach ($fsrpComponents as $component)
                                    <option value="{{ $component->id }}" @selected(old('fsrp_component_id', $plan->fsrp_component_id) == $component->id)>
                                        {{ $component->code }} - {{ $component->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fsrp_component_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="fsrp_subcomponent_id" class="form-label">FSRP Subcomponent</label>
                            <select class="form-select @error('fsrp_subcomponent_id') is-invalid @enderror"
                                id="fsrp_subcomponent_id" name="fsrp_subcomponent_id">
                                <option value="">Select Subcomponent</option>
                                @foreach ($fsrpComponents as $component)
                                    @foreach ($component->subcomponents as $subcomponent)
                                        <option value="{{ $subcomponent->id }}"
                                            data-component-id="{{ $component->id }}"
                                            @selected(old('fsrp_subcomponent_id', $plan->fsrp_subcomponent_id) == $subcomponent->id)>
                                            {{ $subcomponent->code }} - {{ $subcomponent->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('fsrp_subcomponent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Is Launched --}}
                        <div class="col-md-4">
                            <label for="is_launched" class="form-label">Is Launched <span class="text-danger">*</span></label>
                            <select class="form-select @error('is_launched') is-invalid @enderror"
                                id="is_launched" name="is_launched" required>
                                <option value="0" {{ old('is_launched', $plan->is_launched) == false ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_launched', $plan->is_launched) == true ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('is_launched')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estimated Start Date --}}
                        <div class="col-md-4">
                            <label for="estimated_start_date" class="form-label">Estimated Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('estimated_start_date') is-invalid @enderror"
                                id="estimated_start_date" name="estimated_start_date"
                                value="{{ old('estimated_start_date', $plan->estimated_start_date?->format('Y-m-d')) }}" required>
                            @error('estimated_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estimated End Date --}}
                        <div class="col-md-4">
                            <label for="estimated_end_date" class="form-label">Estimated End Date</label>
                            <input type="date" class="form-control @error('estimated_end_date') is-invalid @enderror"
                                id="estimated_end_date" name="estimated_end_date"
                                value="{{ old('estimated_end_date', $plan->estimated_end_date?->format('Y-m-d')) }}" readonly>
                            <small class="text-muted">Auto-calculated based on method</small>
                            @error('estimated_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estimated Budget --}}
                        <div class="col-md-4">
                            <label for="estimated_budget" class="form-label">Estimated Budget (USD)</label>
                            <input type="number" step="0.01" class="form-control @error('estimated_budget') is-invalid @enderror"
                                id="estimated_budget" name="estimated_budget"
                                value="{{ old('estimated_budget', $plan->estimated_budget) }}" placeholder="0.00">
                            @error('estimated_budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Procurement Stage --}}
                        <div class="col-md-4">
                            <label for="stage_id" class="form-label">Procurement Stage</label>
                            <select class="form-select @error('stage_id') is-invalid @enderror"
                                id="stage_id" name="stage_id">
                                <option value="">Select Stage</option>
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}"
                                        {{ old('stage_id', $plan->stage_id) == $stage->id ? 'selected' : '' }}>
                                        {{ $stage->stage_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stage_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Procurement Status --}}
                        <div class="col-md-4">
                            <label for="status_id" class="form-label">Procurement Status</label>
                            <select class="form-select @error('status_id') is-invalid @enderror"
                                id="status_id" name="status_id">
                                <option value="">Select Status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}"
                                        {{ old('status_id', $plan->status_id) == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Step Stage --}}
                        <div class="col-md-6">
                            <label for="step_stage_id" class="form-label">Step Stage</label>
                            <select class="form-select @error('step_stage_id') is-invalid @enderror"
                                id="step_stage_id" name="step_stage_id">
                                <option value="">Select Step Stage</option>
                                @foreach ($stepStages as $stepStage)
                                    <option value="{{ $stepStage->id }}"
                                        {{ old('step_stage_id', $plan->step_stage_id) == $stepStage->id ? 'selected' : '' }}>
                                        {{ $stepStage->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('step_stage_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Step Approval --}}
                        <div class="col-md-6">
                            <label for="step_approval_id" class="form-label">Step Approval Process</label>
                            <select class="form-select @error('step_approval_id') is-invalid @enderror"
                                id="step_approval_id" name="step_approval_id">
                                <option value="">Select Step Approval</option>
                                @foreach ($stepApprovals as $stepApproval)
                                    <option value="{{ $stepApproval->id }}"
                                        {{ old('step_approval_id', $plan->step_approval_id) == $stepApproval->id ? 'selected' : '' }}>
                                        {{ $stepApproval->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('step_approval_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="fw-semibold mb-0">World Bank Procurement Tracking</h6>
                            <small class="text-muted">PPSD, STEP, prior review, no-objection, and contract log fields.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="ppsd_reference" class="form-label">PPSD Reference</label>
                            <input type="text" class="form-control @error('ppsd_reference') is-invalid @enderror"
                                id="ppsd_reference" name="ppsd_reference"
                                value="{{ old('ppsd_reference', $plan->ppsd_reference) }}"
                                placeholder="PPSD reference or package note">
                            @error('ppsd_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="step_plan_id" class="form-label">STEP Plan ID</label>
                            <input type="text" class="form-control @error('step_plan_id') is-invalid @enderror"
                                id="step_plan_id" name="step_plan_id"
                                value="{{ old('step_plan_id', $plan->step_plan_id) }}"
                                placeholder="World Bank STEP package ID">
                            @error('step_plan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="step_plan_status" class="form-label">STEP Status</label>
                            <select class="form-select @error('step_plan_status') is-invalid @enderror"
                                id="step_plan_status" name="step_plan_status">
                                @foreach ([
                                    'not_uploaded' => 'Not Uploaded',
                                    'uploaded' => 'Uploaded',
                                    'under_review' => 'Under Review',
                                    'cleared' => 'Cleared',
                                    'needs_update' => 'Needs Update',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('step_plan_status', $plan->step_plan_status ?? 'not_uploaded') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('step_plan_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="step_last_uploaded_at" class="form-label">STEP Upload Date</label>
                            <input type="date" class="form-control @error('step_last_uploaded_at') is-invalid @enderror"
                                id="step_last_uploaded_at" name="step_last_uploaded_at"
                                value="{{ old('step_last_uploaded_at', $plan->step_last_uploaded_at?->format('Y-m-d')) }}">
                            @error('step_last_uploaded_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="prior_review_required" class="form-label">Prior Review Required</label>
                            <select class="form-select @error('prior_review_required') is-invalid @enderror"
                                id="prior_review_required" name="prior_review_required">
                                <option value="0" @selected(old('prior_review_required', $plan->prior_review_required ? '1' : '0') === '0')>No</option>
                                <option value="1" @selected(old('prior_review_required', $plan->prior_review_required ? '1' : '0') === '1')>Yes</option>
                            </select>
                            @error('prior_review_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="world_bank_no_objection_status" class="form-label">WB No-Objection Status</label>
                            <select class="form-select @error('world_bank_no_objection_status') is-invalid @enderror"
                                id="world_bank_no_objection_status" name="world_bank_no_objection_status">
                                @foreach ([
                                    'pending' => 'Pending',
                                    'submitted' => 'Submitted',
                                    'cleared' => 'Cleared',
                                    'objected' => 'Objected',
                                    'needs_revision' => 'Needs Revision',
                                    'not_required' => 'Not Required',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('world_bank_no_objection_status', $plan->world_bank_no_objection_status ?? 'pending') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('world_bank_no_objection_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="world_bank_no_objection_date" class="form-label">WB No-Objection Date</label>
                            <input type="date" class="form-control @error('world_bank_no_objection_date') is-invalid @enderror"
                                id="world_bank_no_objection_date" name="world_bank_no_objection_date"
                                value="{{ old('world_bank_no_objection_date', $plan->world_bank_no_objection_date?->format('Y-m-d')) }}">
                            @error('world_bank_no_objection_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="procurement_risk_level" class="form-label">Risk Level</label>
                            <select class="form-select @error('procurement_risk_level') is-invalid @enderror"
                                id="procurement_risk_level" name="procurement_risk_level">
                                <option value="">Select Risk</option>
                                @foreach (['low' => 'Low', 'moderate' => 'Moderate', 'substantial' => 'Substantial', 'high' => 'High'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('procurement_risk_level', $plan->procurement_risk_level) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('procurement_risk_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="contract_log_reference" class="form-label">Contract Log Reference</label>
                            <input type="text" class="form-control @error('contract_log_reference') is-invalid @enderror"
                                id="contract_log_reference" name="contract_log_reference"
                                value="{{ old('contract_log_reference', $plan->contract_log_reference) }}"
                                placeholder="Contract logbook or record reference">
                            @error('contract_log_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="procurement_record_notes" class="form-label">Record Notes</label>
                            <textarea class="form-control @error('procurement_record_notes') is-invalid @enderror"
                                id="procurement_record_notes" name="procurement_record_notes" rows="2"
                                placeholder="STEP upload, prior review, or record-keeping notes">{{ old('procurement_record_notes', $plan->procurement_record_notes) }}</textarea>
                            @error('procurement_record_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="3"
                                placeholder="Enter procurement plan description">{{ old('description', $plan->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label for="remarks" class="form-label">Notes</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror"
                                id="remarks" name="remarks" rows="2"
                                placeholder="Additional notes">{{ old('remarks', $plan->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('procurement.plans.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-1"></i> Update Plan
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const activitySelect = document.getElementById('activity_id');
    const subActivitySelect = document.getElementById('sub_activity_id');
    const methodSelect = document.getElementById('method_planned_id');
    const startDateInput = document.getElementById('estimated_start_date');
    const endDateInput = document.getElementById('estimated_end_date');
    const generateCodeBtn = document.getElementById('generateCodeBtn');
    const procurementCodeInput = document.getElementById('procurement_code');
    const fsrpComponentSelect = document.getElementById('fsrp_component_id');
    const fsrpSubcomponentSelect = document.getElementById('fsrp_subcomponent_id');

    // Regenerate procurement code
    generateCodeBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to regenerate the procurement code?')) {
            fetch('{{ route("procurement.plans.generate-code") }}')
                .then(response => response.json())
                .then(data => {
                    procurementCodeInput.value = data.code;
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Load sub-activities when activity changes
    activitySelect.addEventListener('change', function() {
        const activityId = this.value;
        subActivitySelect.innerHTML = '<option value="">Select Sub Activity</option>';

        if (activityId) {
            fetch(`{{ url('procurement/plans/sub-activities') }}/${activityId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(subActivity => {
                        const option = document.createElement('option');
                        option.value = subActivity.id;
                        option.textContent = subActivity.name;
                        subActivitySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Calculate end date when method or start date changes
    function calculateEndDate() {
        const methodOption = methodSelect.options[methodSelect.selectedIndex];
        const startDate = startDateInput.value;

        if (methodOption && methodOption.dataset.targetDays && startDate) {
            const targetDays = parseInt(methodOption.dataset.targetDays);
            const start = new Date(startDate);
            start.setDate(start.getDate() + targetDays);

            const endDate = start.toISOString().split('T')[0];
            endDateInput.value = endDate;
        }
    }

    methodSelect.addEventListener('change', calculateEndDate);
    startDateInput.addEventListener('change', calculateEndDate);

    function syncFsrpSubcomponents() {
        const componentId = fsrpComponentSelect.value;
        let selectedStillVisible = false;

        Array.from(fsrpSubcomponentSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            const visible = !componentId || option.dataset.componentId === componentId;
            option.hidden = !visible;
            if (visible && option.selected) {
                selectedStillVisible = true;
            }
        });

        if (!selectedStillVisible) {
            fsrpSubcomponentSelect.value = '';
        }
    }

    fsrpComponentSelect.addEventListener('change', syncFsrpSubcomponents);
    syncFsrpSubcomponents();
});
</script>
@endpush
