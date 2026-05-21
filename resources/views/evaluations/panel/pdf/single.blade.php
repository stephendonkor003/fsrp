<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Evaluation Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1,
        h2,
        h3 {
            margin: 0 0 6px 0;
        }

        .header {
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }

        .yes {
            background: #16a34a;
            color: #fff;
        }

        .no {
            background: #dc2626;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .section {
            margin-bottom: 18px;
        }

        .notes {
            margin-top: 6px;
            font-size: 11px;
        }

        .overall {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    @php
        $evaluation = $submission->evaluation;
        $isGoods = $evaluation->type === 'goods';
    @endphp

    <div class="header">
        <h2>{{ $submission->procurement->title }}</h2>

        <strong>Evaluation:</strong> {{ $evaluation->name }} <br>
        <strong>Type:</strong> {{ strtoupper($evaluation->type) }} <br>
        <strong>Evaluator:</strong> {{ $submission->evaluator->name }} <br>
        <strong>Submitted:</strong> {{ $submission->submitted_at->format('d M Y, H:i') }}
    </div>

    {{-- ================= APPLICANT ================= --}}
    <h3>Applicant</h3>
    <p>
        <strong>Submission Code:</strong>
        {{ $submission->applicant->procurement_submission_code }} <br>

        <strong>Name:</strong>
        {{ optional($submission->applicant->submitter)->name ?? '—' }}
    </p>

    {{-- ================= SECTIONS ================= --}}
    @foreach ($evaluation->sections as $section)
        @php
            $sectionScore = $submission->sectionScores->firstWhere('evaluation_section_id', $section->id);
        @endphp

        <div class="section">
            <h3>{{ $section->name }}</h3>

            {{-- SERVICES --}}
            @if (!$isGoods)
                <table>
                    <thead>
                        <tr>
                            <th>Criteria</th>
                            <th width="80">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section->criteria as $criteria)
                            @php
                                $cs = $submission->criteriaScores->firstWhere('evaluation_criteria_id', $criteria->id);
                            @endphp
                            <tr>
                                <td>{{ $criteria->name }}</td>
                                <td align="center">
                                    {{ number_format($cs->score ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- GOODS --}}
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Criteria</th>
                            <th width="80">Decision</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section->criteria as $criteria)
                            @php
                                $cs = $submission->criteriaScores->firstWhere('evaluation_criteria_id', $criteria->id);
                            @endphp
                            <tr>
                                <td>{{ $criteria->name }}</td>
                                <td align="center">
                                    @if ($cs?->decision === 1)
                                        <span class="badge yes">YES</span>
                                    @elseif ($cs?->decision === 0)
                                        <span class="badge no">NO</span>
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

            <div class="notes">
                <strong>Strengths:</strong><br>
                {{ $sectionScore->strengths ?? '—' }} <br><br>

                <strong>Weaknesses:</strong><br>
                {{ $sectionScore->weaknesses ?? '—' }}
            </div>
        </div>
    @endforeach

    {{-- ================= OVERALL ================= --}}
    @if (!$isGoods)
        <div class="overall">
            Overall Score:
            {{ number_format($submission->overall_score, 2) }}
        </div>
    @endif

</body>

</html>
