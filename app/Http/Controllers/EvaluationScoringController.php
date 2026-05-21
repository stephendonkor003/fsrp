<?php

namespace App\Http\Controllers;

use App\Models\EvaluationCriteria;
use App\Models\EvaluationCriteriaScore;
use App\Models\EvaluationSectionScore;
use Illuminate\Http\Request;

class EvaluationScoringController extends Controller
{
    public function saveCriteriaScore(Request $request)
{
    $request->validate([
        'submission_id'          => 'required|exists:evaluation_submissions,id',
        'evaluation_criteria_id' => 'required|exists:evaluation_criteria,id',
    ]);

    $criteria = EvaluationCriteria::with('section.evaluation')
        ->findOrFail($request->evaluation_criteria_id);

    $evaluation = $criteria->section->evaluation;

    /* =====================================================
     | GOODS EVALUATION (YES / NO + COMMENT)
     ===================================================== */
    if ($evaluation->type === 'goods') {

        $request->validate([
            'decision' => 'required|boolean',
            'comment'  => 'required|string',
        ]);

        EvaluationCriteriaScore::updateOrCreate(
            [
                'submission_id'          => $request->submission_id,
                'evaluation_criteria_id' => $criteria->id,
            ],
            [
                'decision' => (int) $request->decision,
                'comment'  => $request->comment,
                'score'    => null,
            ]
        );

        // Ensure section record exists (for strengths / weaknesses)
        EvaluationSectionScore::firstOrCreate(
            [
                'submission_id'         => $request->submission_id,
                'evaluation_section_id' => $criteria->section->id,
            ]
        );

        return response()->json(['success' => true]);
    }

    /* =====================================================
     | SERVICES EVALUATION (NUMERIC SCORE)
     ===================================================== */
    $request->validate([
        'score' => 'required|numeric|min:0',
    ]);

    if ($request->score > $criteria->max_score) {
        return response()->json(['error' => 'Score exceeds max'], 422);
    }

    EvaluationCriteriaScore::updateOrCreate(
        [
            'submission_id'          => $request->submission_id,
            'evaluation_criteria_id' => $criteria->id,
        ],
        ['score' => round($request->score, 2)]
    );

    $sectionScore = EvaluationSectionScore::firstOrCreate(
        [
            'submission_id'         => $request->submission_id,
            'evaluation_section_id' => $criteria->section->id,
        ],
        ['section_score' => 0]
    );

    $sectionScore->recalculateSectionScore();

    return response()->json([
        'success'       => true,
        'section_score' => $sectionScore->section_score,
        'overall_score' => $sectionScore->submission->overall_score,
    ]);
}


    public function saveSectionNotes(Request $request)
    {
        $request->validate([
            'submission_id'         => 'required|exists:evaluation_submissions,id',
            'evaluation_section_id' => 'required|exists:evaluation_sections,id',
        ]);

        EvaluationSectionScore::updateOrCreate(
            [
                'submission_id'         => $request->submission_id,
                'evaluation_section_id' => $request->evaluation_section_id,
            ],
            [
                'strengths'  => $request->strengths,
                'weaknesses' => $request->weaknesses,
            ]
        );

        return response()->json(['success' => true]);
    }
}