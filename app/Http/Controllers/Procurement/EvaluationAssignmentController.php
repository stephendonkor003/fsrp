<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\EvaluationAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class EvaluationAssignmentController extends Controller
{
    public function store(Request $request)
    {
        EvaluationAssignment::create(
            $request->validate([
                'procurement_id' => 'required',
                'form_id' => 'required',
                'user_id' => 'required',
                'stage' => 'required'
            ]) + [
                'assigned_by' => auth()->id(),
                'assigned_at' => now()
            ]
        );

        return back()->with('success', 'Evaluator assigned');
    }
}