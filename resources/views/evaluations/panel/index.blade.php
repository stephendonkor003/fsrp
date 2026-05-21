@extends('layouts.app')

@section('content')
    <div class="nxl-container evaluation-panel">

        {{-- ================= HEADER ================= --}}
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="feather-users text-primary me-2"></i>
                    Panel Evaluations
                </h4>
                <p class="text-muted mb-0">
                    Consolidated view of all evaluator decisions per procurement.
                </p>
            </div>
        </div>

        {{-- ================= PROCUREMENT SELECT ================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-end gap-3">
                <div class="flex-grow-1">
                    <label class="form-label fw-semibold">Select Procurement</label>
                    <select id="procurementSelect" class="form-select">
                        <option value="">-- Select Procurement --</option>
                        @foreach ($procurements as $procurement)
                            <option value="{{ $procurement->id }}">
                                {{ $procurement->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- <a href="#" id="bulkPdfBtn" class="btn btn-outline-primary d-none">
                    <i class="feather-download me-1"></i>
                    Download All PDFs
                </a> --}}
            </div>
        </div>

        {{-- ================= DATA ================= --}}
        @foreach ($submissions as $procurementId => $apps)
            <div class="procurement-block d-none" id="procurement-{{ $procurementId }}">

                @foreach ($apps as $applicant)
                    <div class="card shadow-sm mb-4">

                        {{-- APPLICANT HEADER --}}
                        <div class="card-header bg-light fw-bold d-flex justify-content-between">
                            <div>
                                Submission:
                                <span class="badge bg-secondary">
                                    {{ $applicant->procurement_submission_code }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ optional($applicant->submitter)->name ?? '—' }}
                                </small>
                            </div>
                        </div>

                        {{-- ================= EVALUATORS ================= --}}
                        <div class="card-body">

                            @php
                                $evals = $evaluations[$applicant->id] ?? collect();
                            @endphp

                            @forelse ($evals as $submission)
                                @php
                                    $evaluation = $submission->evaluation;
                                    $isGoods = $evaluation->type === 'goods';
                                @endphp

                                <div class="border rounded mb-4">

                                    {{-- EVALUATOR HEADER --}}
                                    <div class="p-3 bg-dark text-white d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $submission->evaluator->name }}</strong><br>
                                            <small>
                                                Submitted {{ $submission->submitted_at->format('d M Y, H:i') }}
                                            </small>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <span class="badge bg-{{ $isGoods ? 'warning text-dark' : 'success' }}">
                                                {{ strtoupper($evaluation->type) }}
                                            </span>

                                            <a href="{{ route('evals.cfg.panel.pdf.single', $submission) }}"
                                                class="btn btn-sm btn-outline-light">
                                                PDF
                                            </a>
                                        </div>
                                    </div>

                                    {{-- EVALUATION DETAILS --}}
                                    <div class="p-3">

                                        @foreach ($evaluation->sections as $section)
                                            @php
                                                $sectionScore = $submission->sectionScores->firstWhere(
                                                    'evaluation_section_id',
                                                    $section->id,
                                                );
                                            @endphp

                                            <div class="mb-3">
                                                <h6 class="fw-bold">
                                                    {{ $section->name }}
                                                </h6>

                                                {{-- SERVICES --}}
                                                @if (!$isGoods)
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Criteria</th>
                                                                <th width="100">Score</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($section->criteria as $criteria)
                                                                @php
                                                                    $cs = $submission->criteriaScores->firstWhere(
                                                                        'evaluation_criteria_id',
                                                                        $criteria->id,
                                                                    );
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $criteria->name }}</td>
                                                                    <td class="text-center">
                                                                        {{ number_format($cs->score ?? 0, 2) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @else
                                                    {{-- GOODS --}}
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Criteria</th>
                                                                <th width="90">Decision</th>
                                                                <th>Comment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($section->criteria as $criteria)
                                                                @php
                                                                    $cs = $submission->criteriaScores->firstWhere(
                                                                        'evaluation_criteria_id',
                                                                        $criteria->id,
                                                                    );
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $criteria->name }}</td>
                                                                    <td class="text-center">
                                                                        @if ($cs?->decision === 1)
                                                                            <span class="badge bg-success">YES</span>
                                                                        @elseif ($cs?->decision === 0)
                                                                            <span class="badge bg-danger">NO</span>
                                                                        @else
                                                                            —
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $cs->comment ?? '—' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif

                                                {{-- SECTION NOTES --}}
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <strong>Strengths</strong>
                                                        <div class="form-control bg-light">
                                                            {{ $sectionScore->strengths ?? '—' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Weaknesses</strong>
                                                        <div class="form-control bg-light">
                                                            {{ $sectionScore->weaknesses ?? '—' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- OVERALL (SERVICES ONLY) --}}
                                        @if (!$isGoods)
                                            <div class="text-end fw-bold">
                                                Overall Score:
                                                <span class="text-primary">
                                                    {{ number_format($submission->overall_score, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            @empty
                                <div class="text-muted">
                                    No evaluations submitted yet.
                                </div>
                            @endforelse

                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('procurementSelect');
        const bulkBtn = document.getElementById('bulkPdfBtn');

        select.addEventListener('change', function() {
            document.querySelectorAll('.procurement-block').forEach(el => {
                el.classList.add('d-none');
            });

            if (!this.value) {
                bulkBtn?.classList.add('d-none');
                return;
            }

            document.getElementById('procurement-' + this.value)
                ?.classList.remove('d-none');

            if (bulkBtn) {
                bulkBtn.href = `/evals/config/panel/pdf/procurement/${this.value}`;
                bulkBtn.classList.remove('d-none');
            }
        });
    });
</script>
@endpush
