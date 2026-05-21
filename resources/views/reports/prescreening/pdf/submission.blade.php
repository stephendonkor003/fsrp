<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prescreening Submission Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1, h2 { margin: 0 0 8px 0; color: #0f172a; }
        .section { margin: 18px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f2f2f2; text-align: left; }
        .muted { color: #666; }
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
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ config('app.name') }} — Prescreening Submission Report</div>
        <div class="meta">
            Submission Code: {{ $submission->procurement_submission_code ?? '—' }} |
            Evaluator: {{ $submission->prescreeningResult?->evaluator?->name ?? '—' }} |
            Date: {{ $submission->prescreeningResult?->evaluated_at?->format('Y-m-d H:i') ?? '—' }}
        </div>
    </div>

    <div class="section">
        <h2>Summary</h2>
        <table>
            <tr>
                <th>Procurement</th>
                <td>{{ $submission->procurement->title ?? '—' }} ({{ $submission->procurement->reference_no ?? '—' }})</td>
            </tr>
            <tr>
                <th>Applicant</th>
                <td>{{ $submission->submitter->name ?? '—' }} ({{ $submission->submitter->email ?? '—' }})</td>
            </tr>
            <tr>
                <th>Template</th>
                <td>{{ $template->name ?? '—' }}</td>
            </tr>
            <tr>
                <th>Evaluator</th>
                <td>{{ $submission->prescreeningResult?->evaluator?->name ?? '—' }}</td>
            </tr>
            <tr>
                <th>Final Status</th>
                <td>{{ ucfirst(str_replace('_', ' ', $submission->status ?? 'pending')) }}</td>
            </tr>
            <tr>
                <th>Evaluated At</th>
                <td>{{ $submission->prescreeningResult?->evaluated_at?->format('Y-m-d H:i') ?? '—' }}</td>
            </tr>
            <tr>
                <th>Submitted At</th>
                <td>{{ $submission->submitted_at?->format('Y-m-d H:i') ?? '—' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Score Summary</h2>
        <table>
            <tr>
                <th>Total Criteria</th>
                <th>Passed</th>
                <th>Failed</th>
            </tr>
            <tr>
                <td>{{ $submission->prescreeningResult?->total_criteria ?? '—' }}</td>
                <td>{{ $submission->prescreeningResult?->passed_criteria ?? '—' }}</td>
                <td>{{ $submission->prescreeningResult?->failed_criteria ?? '—' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Criteria Evaluation</h2>
        <table>
            <tr>
                <th>Criterion</th>
                <th>Pass/Fail</th>
                <th>Remarks</th>
            </tr>
            @forelse ($criteria as $criterion)
                @php $evaluation = $evaluations[$criterion->id] ?? null; @endphp
                <tr>
                    <td>{{ $criterion->name }}</td>
                    <td>{{ $evaluation ? ($evaluation->is_passed ? 'Passed' : 'Failed') : '—' }}</td>
                    <td>{{ $evaluation->remarks ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No criteria available.</td>
                </tr>
            @endforelse
        </table>
    </div>

    <div class="section">
        <h2>Applicant Submission Values</h2>
        <table>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            @forelse ($submission->values as $value)
                <tr>
                    <td>{{ $value->field_key }}</td>
                    <td>{{ $value->value }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">No submission values found.</td>
                </tr>
            @endforelse
        </table>
    </div>
    <div class="footer">
        {{ config('app.name') }} — Confidential Prescreening Report — Generated: {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
