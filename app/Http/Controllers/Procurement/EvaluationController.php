<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\EvaluationResult;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function show(FormSubmission $submission)
    {
        return view('procurement.evaluations.show', compact('submission'));
    }

    public function store(Request $request, FormSubmission $submission)
    {
        foreach ($request->scores as $field => $score) {
            EvaluationResult::updateOrCreate(
                [
                    'submission_id' => $submission->id,
                    'field_key' => $field,
                    'evaluator_id' => auth()->id()
                ],
                [
                    'form_id' => $submission->form_id,
                    'score' => $score,
                    'comment' => $request->comments[$field] ?? null,
                    'evaluated_at' => now()
                ]
            );
        }

        return back()->with('success', 'Evaluation submitted');
    }
}