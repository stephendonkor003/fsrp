<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Evaluation Submission Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1, h2 { margin: 0 0 8px 0; color: #0f172a; }
        .section { margin: 18px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f2f2f2; text-align: left; }
        .header {
            background: #0f172a;
            color: #fff;
            padding: 14px 18px;
            border-bottom: 4px solid #22c55e;
        }
        .header .title { font-size: 18px; font-weight: bold; }
        .header .meta { font-size: 11px; margin-top: 6px; color: #e2e8f0; }
        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 42px;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 10px;
            padding: 10px 16px;
            border-top: 3px solid #f59e0b;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            background: #1d4ed8;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ config('app.name') }} - Evaluation Submission Report</div>
        <div class="meta">
            Submission Code: {{ $submission->applicant?->procurement_submission_code ?? 'N/A' }} |
            Evaluator: {{ $submission->evaluator?->name ?? 'N/A' }} |
            Submitted: {{ $submission->submitted_at?->format('Y-m-d H:i') ?? 'N/A' }}
        </div>
    </div>

    <div class="section">
        <h2>Summary</h2>
        <table>
            <tr>
                <th>Procurement</th>
                <td>{{ $submission->procurement->title ?? 'N/A' }} ({{ $submission->procurement->reference_no ?? 'N/A' }})</td>
            </tr>
            <tr>
                <th>Applicant</th>
                <td>{{ $submission->applicant?->submitter?->name ?? 'N/A' }} ({{ $submission->applicant?->submitter?->email ?? 'N/A' }})</td>
            </tr>
            <tr>
                <th>Evaluation</th>
                <td>{{ $submission->evaluation->name ?? 'N/A' }} <span class="badge">{{ strtoupper($submission->evaluation->type ?? 'N/A') }}</span></td>
            </tr>
            <tr>
                <th>Evaluator</th>
                <td>{{ $submission->evaluator?->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Submitted At</th>
                <td>{{ $submission->submitted_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    @php
        $isGoods = $submission->evaluation?->type === 'goods';
    @endphp

    @foreach ($submission->evaluation->sections as $section)
        @php
            $sectionScore = $submission->sectionScores->firstWhere('evaluation_section_id', $section->id);
        @endphp
        <div class="section">
            <h2>{{ $section->name }}</h2>
            @if (!$isGoods)
                <table>
                    <tr>
                        <th>Criteria</th>
                        <th>Max</th>
                        <th>Score</th>
                    </tr>
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
                </table>
            @else
                <table>
                    <tr>
                        <th>Criteria</th>
                        <th>Decision</th>
                        <th>Comment</th>
                    </tr>
                    @foreach ($section->criteria as $criteria)
                        @php
                            $score = $submission->criteriaScores->firstWhere('evaluation_criteria_id', $criteria->id);
                        @endphp
                        <tr>
                            <td>{{ $criteria->name }}</td>
                            <td>{{ $score?->decision === 1 ? 'YES' : ($score?->decision === 0 ? 'NO' : 'N/A') }}</td>
                            <td>{{ $score->comment ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <table>
                <tr>
                    <th>Strengths</th>
                    <td>{{ $sectionScore->strengths ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Weaknesses</th>
                    <td>{{ $sectionScore->weaknesses ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    @endforeach

    @if (!$isGoods)
        <div class="section">
            <h2>Overall Score</h2>
            <table>
                <tr>
                    <th>Score</th>
                    <td>
                        {{ number_format($submission->overall_score ?? 0, 2) }}
                        @if ($overallMax)
                            / {{ $overallMax }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">
        {{ config('app.name') }} - Confidential Evaluation Report - Generated: {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
