<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Procurement Evaluation Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0 0 6px 0;
        }

        .cover {
            text-align: center;
            margin-bottom: 40px;
        }

        .cover h1 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .cover p {
            font-size: 13px;
        }

        .submission {
            page-break-after: always;
        }

        .header {
            border-bottom: 2px solid #000;
            margin-bottom: 12px;
            padding-bottom: 8px;
        }

        .meta {
            font-size: 11px;
            margin-bottom: 10px;
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
            margin-bottom: 10px;
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
            margin-bottom: 16px;
        }

        .notes {
            font-size: 11px;
            margin-top: 6px;
        }

        .overall {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin-top: 8px;
        }

        .evaluator-block {
            border: 1px solid #bbb;
            padding: 10px;
            margin-bottom: 14px;
        }

        .evaluator-header {
            font-weight: bold;
            margin-bottom: 6px;
        }

        .small {
            font-size: 10px;
            color: #444;
        }
    </style>
</head>

<body>

    {{-- ================= COVER PAGE ================= --}}
    <div class="cover">
        <h1>{{ $procurement->title }}</h1>
        <p>
            Procurement Evaluation Report<br>
            Generated on {{ now()->format('d M Y, H:i') }}
        </p>
    </div>

    {{-- ================= SUBMISSIONS ================= --}}
    @foreach ($submissions as $submission)
        @php
            $evaluation = $submission->evaluation;
            $isGoods = $evaluation->type === 'goods';
        @endphp

        <div class="submission">

            {{-- HEADER --}}
            <div class="header">
                <h2>Applicant: {{ optional($submission->applicant->submitter)->name ?? '—' }}</h2>

                <div class="meta">
                    <strong>Submission Code:</strong>
                    {{ $submission->applicant->procurement_submission_code }} <br>

                    <strong>Evaluation:</strong>
                    {{ $evaluation->name }} <br>

                    <strong>Type:</strong>
                    {{ strtoupper($evaluation->type) }}
                </div>
            </div>

            {{-- ================= EVALUATORS ================= --}}
            @foreach ($submission->groupedEvaluators as $eval)
                <div class="evaluator-block">

                    <div class="evaluator-header">
                        Evaluator: {{ $eval->evaluator->name }}
                    </div>

                    <div class="small">
                        Submitted: {{ $eval->submitted_at->format('d M Y, H:i') }}
                    </div>

                    {{-- ================= SECTIONS ================= --}}
                    @foreach ($evaluation->sections as $section)
                        @php
                            $sectionScore = $eval->sectionScores->firstWhere('evaluation_section_id', $section->id);
                        @endphp

                        <div class="section">
                            <h4>{{ $section->name }}</h4>

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
                                                $cs = $eval->criteriaScores->firstWhere(
                                                    'evaluation_criteria_id',
                                                    $criteria->id,
                                                );
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
                                                $cs = $eval->criteriaScores->firstWhere(
                                                    'evaluation_criteria_id',
                                                    $criteria->id,
                                                );
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

                            {{-- SECTION NOTES --}}
                            <div class="notes">
                                <strong>Strengths:</strong><br>
                                {{ $sectionScore->strengths ?? '—' }} <br><br>

                                <strong>Weaknesses:</strong><br>
                                {{ $sectionScore->weaknesses ?? '—' }}
                            </div>
                        </div>
                    @endforeach

                    {{-- OVERALL --}}
                    @if (!$isGoods)
                        <div class="overall">
                            Overall Score:
                            {{ number_format($eval->overall_score, 2) }}
                        </div>
                    @endif

                </div>
            @endforeach

        </div>
    @endforeach

</body>

</html>
