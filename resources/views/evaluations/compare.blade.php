@extends('layouts.app')

@section('content')
    <div class="nxl-container evaluation-compare">

        {{-- ================= PAGE HEADER ================= --}}
        <div class="page-header mb-4">
            <h4 class="fw-bold">Panel Evaluation Comparison</h4>
            <p class="text-muted mb-0">
                Independent evaluator scores have been aggregated and ranked objectively.
            </p>
        </div>

        {{-- ================= EXECUTIVE SUMMARY ================= --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card soft-card text-center p-3">
                    <small class="text-muted">Total Applicants</small>
                    <h3 class="fw-bold text-primary">{{ $comparisons->count() }}</h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card soft-card text-center p-3">
                    <small class="text-muted">Panel Evaluators</small>
                    <h3 class="fw-bold text-success">
                        {{ optional($comparisons->first())['evaluations']?->count() ?? 0 }}
                    </h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card soft-card text-center p-3">
                    <small class="text-muted">Panel Average Score</small>
                    <h3 class="fw-bold text-info">
                        {{ number_format($comparisons->avg('average'), 2) }}
                    </h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card soft-card text-center p-3">
                    <small class="text-muted">High Disagreement Cases</small>
                    <h3 class="fw-bold text-danger">
                        {{ $comparisons->where('spread', '>', 15)->count() }}
                    </h3>
                </div>
            </div>
        </div>

        {{-- ================= RANKING TABLE ================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                Applicant Ranking Overview
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">Rank</th>
                            <th>Submission Code</th>
                            <th class="text-center">Average</th>
                            <th class="text-center">Highest</th>
                            <th class="text-center">Lowest</th>
                            <th class="text-center">Spread</th>
                            <th class="text-center">Evaluators</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comparisons as $index => $row)
                            <tr>
                                <td class="text-center fw-bold">
                                    <span class="badge bg-dark">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $row['submission_code'] }}
                                    </span>
                                    @if ($index === 0)
                                        <span class="badge bg-success ms-2">
                                            <i class="feather-award me-1"></i> Top Ranked
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold text-primary">{{ $row['average'] }}</td>
                                <td class="text-center text-success">{{ $row['highest'] }}</td>
                                <td class="text-center text-danger">{{ $row['lowest'] }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $row['spread'] > 15 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $row['spread'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{ $row['evaluations']->count() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= DETAILED ANALYSIS ================= --}}
        @foreach ($comparisons as $rank => $row)
            <div class="card shadow-sm mb-4 border-start border-4 border-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>
                        Rank {{ $rank + 1 }} — {{ $row['submission_code'] }}
                    </strong>
                    <span class="badge bg-primary">
                        Avg Score: {{ $row['average'] }}
                    </span>
                </div>

                <div class="card-body">

                    <table class="table table-sm table-bordered mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Evaluator</th>
                                <th class="text-center">Score</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($row['evaluations'] as $evaluation)
                                <tr>
                                    <td>{{ $evaluation->evaluator->name }}</td>
                                    <td class="text-center fw-bold">
                                        {{ $evaluation->overall_score }}
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Submitted</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-outline-secondary btn-sm"
                            onclick="toggleChart('{{ $row['submission_code'] }}')">
                            <i class="feather-bar-chart-2 me-1"></i>
                            View Score Chart
                        </button>
                    </div>

                    <div class="mt-4 d-none" id="chart-{{ $row['submission_code'] }}">
                        <canvas height="120"></canvas>
                    </div>

                </div>
            </div>
        @endforeach

    </div>

    {{-- ================= JS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const comparisons = @json($comparisons);

        function toggleChart(code) {
            const el = document.getElementById('chart-' + code);
            el.classList.toggle('d-none');

            if (!el.dataset.loaded) {
                renderChart(el, code);
                el.dataset.loaded = true;
            }
        }

        function renderChart(container, code) {
            const ctx = container.querySelector('canvas').getContext('2d');
            const row = comparisons.find(
                r => r.submission_code === code
            );

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: row.evaluations.map(e => e.evaluator.name),
                    datasets: [{
                        data: row.evaluations.map(e => e.overall_score),
                        backgroundColor: '#0d6efd'
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    </script>
@endsection
