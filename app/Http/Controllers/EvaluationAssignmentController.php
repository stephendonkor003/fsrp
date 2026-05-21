<?php

namespace App\Http\Controllers;

use App\Models\EvaluationAssignment;
use App\Models\Evaluation;
use App\Models\FormSubmission;
use App\Models\Procurement;
use App\Models\User;
use App\Mail\EvaluationAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EvaluationAssignmentController extends Controller
{
    /**
     * =====================================================
     * ASSIGNMENT HUB
     * Lists ALL procurements as accordions
     * =====================================================
     */
    public function hub()
    {
        $procurements = Procurement::with([
            'evaluationAssignments.evaluator',
            'evaluationAssignments.evaluation',
            'evaluationAssignments.submission',
            'submissions',
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        // Only ACTIVE evaluations can be assigned
        $evaluations = Evaluation::where('status', 'active')->get();

        // ✅ ANY user can be an evaluator
        $evaluators = User::orderBy('name')->get();

        return view('evaluations.assign-hub', compact(
            'procurements',
            'evaluations',
            'evaluators'
        ));
    }

    /**
     * =====================================================
     * STORE ASSIGNMENT
     * =====================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'evaluation_id'  => 'required|exists:evaluations,id',
            'procurement_id' => 'required|exists:procurements,id',
            'user_id'        => 'required|exists:users,id',
            'assignment_type' => 'required|in:procurement,submission',
            'submission_id' => 'required_if:assignment_type,submission|nullable|exists:form_submissions,id',
        ]);

        // Prevent assignment to CLOSED evaluations
        $evaluation = Evaluation::findOrFail($request->evaluation_id);
        if ($evaluation->status === 'close') {
            return back()->with('error', 'Cannot assign evaluators to a closed evaluation.');
        }

        // Prevent duplicate assignment
        $exists = EvaluationAssignment::where([
            'evaluation_id'  => $request->evaluation_id,
            'procurement_id' => $request->procurement_id,
            'user_id'        => $request->user_id,
            'form_submission_id' => $request->assignment_type === 'submission'
                ? $request->submission_id
                : null,
        ])->exists();

        if ($exists) {
            return back()->with('error', 'This user is already assigned as an evaluator.');
        }

        $submission = null;
        if ($request->assignment_type === 'submission') {
            $submission = FormSubmission::where('id', $request->submission_id)
                ->where('procurement_id', $request->procurement_id)
                ->first();
            if (!$submission) {
                return back()->with('error', 'Selected submission does not belong to this procurement.');
            }
        }

        EvaluationAssignment::create([
            'evaluation_id'  => $request->evaluation_id,
            'procurement_id' => $request->procurement_id,
            'form_submission_id' => $request->assignment_type === 'submission'
                ? $request->submission_id
                : null,
            'user_id'        => $request->user_id,
            'assigned_by'    => Auth::id(),
            'assigned_at'    => now(),
            'status'         => 'assigned',
        ]);

        $evaluator = User::find($request->user_id);
        $procurement = Procurement::find($request->procurement_id);
        $evaluation = Evaluation::find($request->evaluation_id);
        if ($evaluator?->email) {
            Mail::to($evaluator->email)->send(
                new EvaluationAssigned($evaluator, $evaluation, $procurement, $submission)
            );
        }

        return back()->with('success', 'Evaluator assigned successfully.');
    }

    /**
     * =====================================================
     * REMOVE ASSIGNMENT
     * =====================================================
     */
    public function destroy(EvaluationAssignment $assignment)
    {
        // Governance rule: do not remove after submission
        if ($assignment->status === 'submitted') {
            return back()->with('error', 'Cannot remove evaluator after submission.');
        }

        $assignment->delete();

        return back()->with('success', 'Evaluator removed successfully.');
    }
}
