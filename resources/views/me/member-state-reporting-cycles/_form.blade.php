@php
    $editing = isset($cycle) && $cycle?->exists;
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please correct the highlighted information.</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <strong>{{ $editing ? 'Reporting cycle settings' : 'New reporting cycle' }}</strong>
    </div>
    <div class="card-body">
        @if ($editing)
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-light">
                        <div class="small text-muted text-uppercase fw-semibold">Frequency</div>
                        <div class="fw-bold mt-1">{{ $cycle->reportingFrequency?->name }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-light">
                        <div class="small text-muted text-uppercase fw-semibold">Reporting period</div>
                        <div class="fw-bold mt-1">{{ $cycle->display_label }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-light">
                        <div class="small text-muted text-uppercase fw-semibold">Country reports</div>
                        <div class="fw-bold mt-1">{{ $cycle->submissions_count }}</div>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold" for="reporting_frequency_id">Reporting frequency</label>
                    <select name="reporting_frequency_id" id="reporting_frequency_id"
                        class="form-select @error('reporting_frequency_id') is-invalid @enderror" required>
                        <option value="">Select frequency</option>
                        @foreach ($frequencies as $frequency)
                            <option value="{{ $frequency->id }}" data-code="{{ $frequency->code }}"
                                @selected(old('reporting_frequency_id') === $frequency->id)>
                                {{ $frequency->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Only Quarterly, Semi-Annual, and Annual are available to Member States.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="reporting_year">Reporting year</label>
                    <input type="number" name="reporting_year" id="reporting_year"
                        min="2000" max="{{ now()->year + 10 }}" value="{{ old('reporting_year', now()->year) }}"
                        class="form-control @error('reporting_year') is-invalid @enderror" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" for="period_number">Period</label>
                    <select name="period_number" id="period_number"
                        class="form-select @error('period_number') is-invalid @enderror" required>
                        <option value="">Select a frequency first</option>
                    </select>
                    <div class="form-text">The period key and dates are generated automatically.</div>
                </div>
            </div>
        @endif

        <hr class="my-4">

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold" for="status">Cycle status</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach (\App\Models\MemberStateReportingCycle::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $cycle->status ?? 'draft') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="form-text">Only an Open cycle appears as selectable in the Member State portal.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold" for="opens_at">Portal opens at</label>
                <input type="datetime-local" name="opens_at" id="opens_at"
                    value="{{ old('opens_at', isset($cycle) && $cycle->opens_at ? $cycle->opens_at->format('Y-m-d\\TH:i') : '') }}"
                    class="form-control @error('opens_at') is-invalid @enderror">
                <div class="form-text">Leave blank to open immediately when status is Open.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold" for="closes_at">Portal closes at</label>
                <input type="datetime-local" name="closes_at" id="closes_at"
                    value="{{ old('closes_at', isset($cycle) && $cycle->closes_at ? $cycle->closes_at->format('Y-m-d\\TH:i') : '') }}"
                    class="form-control @error('closes_at') is-invalid @enderror">
                <div class="form-text">Leave blank for no automatic closing date.</div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold" for="instructions">Instructions for Member States</label>
                <textarea name="instructions" id="instructions" rows="4"
                    class="form-control @error('instructions') is-invalid @enderror"
                    placeholder="Optional guidance shown with this reporting period">{{ old('instructions', $cycle->instructions ?? '') }}</textarea>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <a href="{{ route('budget.me.member-state-reporting-cycles.index') }}" class="btn btn-light border">
            <i class="feather-arrow-left me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="feather-save me-1"></i> {{ $editing ? 'Save cycle' : 'Create cycle' }}
        </button>
    </div>
</div>

@unless ($editing)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var frequency = document.getElementById('reporting_frequency_id');
                var period = document.getElementById('period_number');
                var previous = @json((string) old('period_number', ''));
                var choices = {
                    QUARTERLY: [['1', 'Quarter 1'], ['2', 'Quarter 2'], ['3', 'Quarter 3'], ['4', 'Quarter 4']],
                    SEMI_ANNUAL: [['1', 'First Half (H1)'], ['2', 'Second Half (H2)']],
                    ANNUAL: [['1', 'Annual']]
                };

                function refreshPeriods() {
                    var selected = frequency.options[frequency.selectedIndex];
                    var code = selected ? selected.getAttribute('data-code') : '';
                    var options = choices[code] || [];
                    period.innerHTML = '';

                    if (!options.length) {
                        period.add(new Option('Select a frequency first', ''));
                        return;
                    }

                    options.forEach(function (item) {
                        var option = new Option(item[1], item[0], false, item[0] === previous);
                        period.add(option);
                    });

                    if (!previous && options.length === 1) {
                        period.value = options[0][0];
                    }
                }

                frequency.addEventListener('change', function () {
                    previous = '';
                    refreshPeriods();
                });
                refreshPeriods();
            });
        </script>
    @endpush
@endunless
