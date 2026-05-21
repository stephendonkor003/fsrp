@extends('layouts.app')

@section('content')
    <div class="nxl-container">

        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Evaluation Submission Report</h4>
                <p class="text-muted mb-0">Detailed scoring, comments, and section summaries.</p>
            </div>
            <a href="{{ route('reports.evaluations.index') }}" class="btn btn-outline-secondary btn-sm">
                Back to Reports
            </a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="fw-semibold">Procurement</div>
                        <div>{{ $submission->procurement->title ?? 'N/A' }}</div>
                        <div class="text-muted">{{ $submission->procurement->reference_no ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold">Submission Code</div>
                        <div>{{ $submission->applicant?->procurement_submission_code ?? 'N/A' }}</div>
                        <div class="text-muted">Applicant: {{ $submission->applicant?->submitter?->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold">Evaluation</div>
                        <div>{{ $submission->evaluation->name ?? 'N/A' }}</div>
                        <div class="text-muted">
                            Evaluator: {{ $submission->evaluator?->name ?? 'N/A' }}<br>
                            Submitted: {{ $submission->submitted_at?->format('d M Y, H:i') ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $isGoods = $submission->evaluation?->type === 'goods';
            $colors = ['primary', 'success', 'info', 'warning', 'danger'];
        @endphp

        @foreach ($submission->evaluation->sections as $i => $section)
            @php
                $sectionScore = $submission->sectionScores->firstWhere('evaluation_section_id', $section->id);
            @endphp
            <div class="card shadow-sm mb-4 border-start border-{{ $colors[$i % 5] }} border-4">
                <div class="card-header bg-{{ $colors[$i % 5] }} text-white d-flex justify-content-between">
                    <span>{{ $section->name }}</span>
                </div>
                <div class="card-body">
                    @if (!$isGoods)
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Criteria</th>
                                    <th width="120">Max</th>
                                    <th width="120">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($section->criteria as $criteria)
                                    @php
                                        $score = $submission->criteriaScores->firstWhere('evaluation_criteria_id', $criteria->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $criteria->name }}</td>
                                        <td>{{ $criteria->max_score }}</td>
                                        <td>{{ number_format($score->score ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Criteria</th>
                                    <th width="120">Decision</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($section->criteria as $criteria)
                                    @php
                                        $score = $submission->criteriaScores->firstWhere('evaluation_criteria_id', $criteria->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $criteria->name }}</td>
                                        <td>
                                            @if ($score?->decision === 1)
                                                <span class="badge bg-success">YES</span>
                                            @elseif($score?->decision === 0)
                                                <span class="badge bg-danger">NO</span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $score->comment ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Strengths</strong>
                            <div class="form-control bg-light">{{ $sectionScore->strengths ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong>Weaknesses</strong>
                            <div class="form-control bg-light">{{ $sectionScore->weaknesses ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if (!$isGoods)
            <div class="card shadow-sm border-success">
                <div class="card-body text-end fw-bold">
                    Overall Score:
                    <span class="text-success">{{ number_format($submission->overall_score ?? 0, 2) }}</span>
                    @if ($overallMax)
                        / {{ $overallMax }}
                    @endif
                </div>
            </div>
        @endif

        <div class="mt-4 text-end">
            <a href="{{ route('reports.evaluations.submission.pdf', $submission) }}" class="btn btn-success">
                Download PDF
            </a>
        </div>
    </div>
@endsection
