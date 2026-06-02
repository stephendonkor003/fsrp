@extends('layouts.app')

@section('content')
    <div class="nxl-container">
        <div class="page-header d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="page-title mb-1">Safeguards, Stakeholder Engagement & GRM</h4>
                <p class="text-muted mb-0">Environmental and social screening, engagement follow-up, and grievance resolution workflow.</p>
            </div>
        </div>

        <div class="row g-3 mb-3">
            @foreach ([
                ['label' => 'Screenings', 'value' => $summary['screenings'], 'class' => 'primary'],
                ['label' => 'High-Risk Screenings', 'value' => $summary['high_risk_screenings'], 'class' => 'danger'],
                ['label' => 'Open Engagements', 'value' => $summary['engagements_open'], 'class' => 'info'],
                ['label' => 'Open GRM Cases', 'value' => $summary['grievances_open'], 'class' => 'warning'],
            ] as $card)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <small class="text-muted">{{ $card['label'] }}</small>
                            <div class="h3 mb-0 text-{{ $card['class'] }}">{{ number_format($card['value']) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3">
            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">New Safeguard Screening</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('fsrp.safeguards.screenings.store') }}" class="row g-2 fsrp-taxonomy-form">
                            @csrf
                            <div class="col-12">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            @include('fsrp.safeguards.partials.taxonomy-fields')
                            <div class="col-md-6">
                                <label class="form-label">Activity</label>
                                <select name="activity_id" class="form-select">
                                    <option value="">N/A</option>
                                    @foreach ($activities as $activity)
                                        <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sub-Activity</label>
                                <select name="sub_activity_id" class="form-select">
                                    <option value="">N/A</option>
                                    @foreach ($subActivities as $subActivity)
                                        <option value="{{ $subActivity->id }}">{{ $subActivity->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Procurement Package</label>
                                <select name="procurement_plan_id" class="form-select">
                                    <option value="">N/A</option>
                                    @foreach ($procurementPlans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->procurement_code }} - {{ Str::limit($plan->title, 50) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Work Plan</label>
                                <select name="approved_work_plan_id" class="form-select">
                                    <option value="">N/A</option>
                                    @foreach ($workPlans as $workPlan)
                                        <option value="{{ $workPlan->id }}">{{ $workPlan->awp_code }} - {{ Str::limit($workPlan->title, 50) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Risk</label>
                                <select name="risk_level" class="form-select" required>
                                    @foreach (['low' => 'Low', 'moderate' => 'Moderate', 'substantial' => 'Substantial', 'high' => 'High'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="screening_status" class="form-select" required>
                                    @foreach (['draft' => 'Draft', 'screened' => 'Screened', 'mitigation_required' => 'Mitigation Required', 'cleared' => 'Cleared', 'closed' => 'Closed'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Screened On</label>
                                <input type="date" name="screened_on" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Screener</label>
                                <select name="screened_by" class="form-select">
                                    <option value="">N/A</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Environmental Risks</label>
                                <textarea name="environmental_risks" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Social Risks</label>
                                <textarea name="social_risks" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mitigation Measures</label>
                                <textarea name="mitigation_measures" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Evidence Reference</label>
                                <input type="text" name="evidence_reference" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Next Review Due</label>
                                <input type="date" name="next_review_due_on" class="form-control">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Save Screening</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">New Stakeholder Engagement</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('fsrp.safeguards.engagements.store') }}" class="row g-2 fsrp-taxonomy-form">
                            @csrf
                            <div class="col-12">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            @include('fsrp.safeguards.partials.taxonomy-fields')
                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="engagement_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stakeholder Group</label>
                                <input type="text" name="stakeholder_group" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Participants</label>
                                <input type="number" min="0" name="participants_count" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Summary</label>
                                <textarea name="summary" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Commitments Made</label>
                                <textarea name="commitments_made" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Follow-Up Actions</label>
                                <textarea name="follow_up_actions" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Follow-Up Due</label>
                                <input type="date" name="follow_up_due_on" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'overdue' => 'Overdue'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Save Engagement</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">New GRM Case</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('fsrp.safeguards.grievances.store') }}" class="row g-2 fsrp-taxonomy-form">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Complainant</label>
                                <input type="text" name="complainant_name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact</label>
                                <input type="text" name="complainant_contact" class="form-control">
                            </div>
                            @include('fsrp.safeguards.partials.taxonomy-fields')
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" required>
                                    @foreach (['general' => 'General', 'environmental' => 'Environmental', 'social' => 'Social', 'procurement' => 'Procurement', 'labor' => 'Labor', 'gbv' => 'GBV', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select" required>
                                    @foreach (['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'critical' => 'Critical'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Received On</label>
                                <input type="date" name="received_on" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Assigned To</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">N/A</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Resolution Actions</label>
                                <textarea name="resolution_actions" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due On</label>
                                <input type="date" name="due_on" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach (['open' => 'Open', 'assigned' => 'Assigned', 'investigating' => 'Investigating', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Closure Notes</label>
                                <textarea name="closure_notes" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Save GRM Case</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Safeguard Screening Register</h5>
            </div>
            <div class="card-body">
                <x-data-table id="safeguardScreeningsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>FSRP</th>
                            <th>Linked Record</th>
                            <th>Risk</th>
                            <th>Status</th>
                            <th>Next Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($screenings as $screening)
                            <tr>
                                <td class="fw-semibold text-primary">{{ $screening->screening_code }}</td>
                                <td>{{ $screening->title }}</td>
                                <td>{{ $screening->component?->code ?: 'N/A' }} {{ $screening->subcomponent?->code ? '/ ' . $screening->subcomponent->code : '' }}</td>
                                <td>
                                    {{ $screening->activity?->name ?: $screening->procurementPlan?->procurement_code ?: $screening->workPlan?->awp_code ?: 'N/A' }}
                                </td>
                                <td>{{ ucfirst($screening->risk_level) }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $screening->screening_status)) }}</td>
                                <td>{{ $screening->next_review_due_on?->format('Y-m-d') ?: 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-data-table>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-xl-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Stakeholder Engagement Register</h5>
                    </div>
                    <div class="card-body">
                        <x-data-table id="stakeholderEngagementsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Group</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Follow-Up Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($engagements as $engagement)
                                    <tr>
                                        <td class="fw-semibold text-primary">{{ $engagement->engagement_code }}</td>
                                        <td>{{ $engagement->title }}</td>
                                        <td>{{ $engagement->stakeholder_group ?: 'N/A' }}</td>
                                        <td>{{ $engagement->engagement_date?->format('Y-m-d') ?: 'N/A' }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $engagement->status)) }}</td>
                                        <td>{{ $engagement->follow_up_due_on?->format('Y-m-d') ?: 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-data-table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">GRM Register</h5>
                    </div>
                    <div class="card-body">
                        <x-data-table id="grmCasesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Case</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Assigned</th>
                                    <th>Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grievances as $grievance)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-primary">{{ $grievance->case_code }}</div>
                                            <small class="text-muted">{{ Str::limit($grievance->description, 60) }}</small>
                                        </td>
                                        <td>{{ ucfirst($grievance->category) }}</td>
                                        <td>{{ ucfirst($grievance->priority) }}</td>
                                        <td>{{ ucfirst($grievance->status) }}</td>
                                        <td>{{ $grievance->assignee?->name ?: 'N/A' }}</td>
                                        <td>{{ $grievance->due_on?->format('Y-m-d') ?: 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-data-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.fsrp-taxonomy-form').forEach((form) => {
                const componentSelect = form.querySelector('[data-fsrp-component]');
                const subcomponentSelect = form.querySelector('[data-fsrp-subcomponent]');

                const sync = () => {
                    const componentId = componentSelect?.value || '';
                    let selectedStillVisible = false;

                    Array.from(subcomponentSelect?.options || []).forEach((option) => {
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

                    if (!selectedStillVisible && subcomponentSelect) {
                        subcomponentSelect.value = '';
                    }
                };

                componentSelect?.addEventListener('change', sync);
                sync();
            });
        });
    </script>
@endpush
