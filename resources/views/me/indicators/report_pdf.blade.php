<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>M&E Management Report Sheet</title>
    <style>
        @page { margin: 12px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 6.8px;
            color: #111827;
        }
        .header {
            border-bottom: 2px solid #334155;
            margin-bottom: 8px;
            padding-bottom: 6px;
            text-align: center;
        }
        .app-name {
            font-size: 11px;
            font-weight: 700;
            margin: 0 0 2px 0;
            color: #0f172a;
        }
        .title {
            font-size: 12px;
            font-weight: bold;
            margin: 0;
            color: #0f172a;
        }
        .meta {
            margin-top: 4px;
            font-size: 7px;
            color: #334155;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 2px 3px;
            vertical-align: top;
            word-break: break-word;
        }
        th {
            background: #e2e8f0;
            font-size: 6.5px;
            text-align: left;
        }
        .small { font-size: 6.5px; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="header">
        <p class="app-name">{{ config('app.name', 'Application') }}</p>
        <p class="title">M&E Management Report Sheet</p>
        <div class="meta">
            Generated: {{ $generatedAt?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s') }}
            @if (!empty($searchTerm))
                | Filter: "{{ $searchTerm }}"
            @endif
            | Total Rows: {{ $rows->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Program</th>
                <th>Project</th>
                <th>Activity</th>
                <th>Sub-Activity</th>
                <th>Indicator</th>
                <th>Owner</th>
                <th>FSRP Component</th>
                <th>FSRP Subcomponent</th>
                <th>Level</th>
                <th>Frequency</th>
                <th>Baseline</th>
                <th>Disaggregation</th>
                <th>LOP Target</th>
                <th>Period Target</th>
                <th>Period Achieved</th>
                <th>Period %</th>
                <th>LOP %</th>
                <th>Responsible</th>
                <th>Methodology</th>
                <th>Source</th>
                <th>Definition</th>
                <th>Target</th>
                <th>Actual</th>
                <th>Achievement</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['program'] ?? 'N/A' }}</td>
                    <td>{{ $row['project'] ?? 'N/A' }}</td>
                    <td>{{ $row['activity'] ?? 'N/A' }}</td>
                    <td>{{ $row['sub_activity'] ?? 'N/A' }}</td>
                    <td>{{ $row['indicator_name'] ?? 'N/A' }}</td>
                    <td>{{ $row['owner_type'] ?? 'N/A' }}</td>
                    <td>{{ $row['fsrp_component'] ?? 'N/A' }}</td>
                    <td>{{ $row['fsrp_subcomponent'] ?? 'N/A' }}</td>
                    <td>{{ $row['indicator_level'] ?? 'N/A' }}</td>
                    <td>{{ $row['frequency'] ?? 'N/A' }}</td>
                    <td>
                        {{ $row['baseline_type'] ?? 'N/A' }} /
                        {{ $row['baseline_period'] ?? 'N/A' }} /
                        {{ $row['baseline_value'] ?? 'N/A' }}
                    </td>
                    <td>{{ $row['disaggregation'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['lop_target'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['reporting_period_target'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['reporting_period_achievement'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['reporting_period_performance'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['lop_performance'] ?? 'N/A' }}</td>
                    <td>{{ $row['responsible'] ?? 'N/A' }}</td>
                    <td>{{ $row['methodology'] ?? 'N/A' }}</td>
                    <td>{{ $row['primary_source_type'] ?? 'N/A' }}: {{ $row['primary_source_value'] ?? 'N/A' }}</td>
                    <td>{{ $row['definition'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['target'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['actual'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['achievement'] ?? 'N/A' }}</td>
                    <td class="nowrap">{{ $row['status'] ?? 'N/A' }}</td>
                    <td>{{ $row['performance_remarks'] ?? 'N/A' }}</td>
                    <td>{{ $row['notes'] ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="27" class="small">No indicators available for export.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
