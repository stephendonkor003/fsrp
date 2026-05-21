<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Procurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementEvaluationController extends Controller
{
    public function create(Procurement $procurement)
    {
        $evaluations = Evaluation::where('status', 'active')->get();

        return view('procurements.evaluations.assign', compact(
            'procurement',
            'evaluations'
        ));
    }

    public function store(Request $request, Procurement $procurement)
    {
        $request->validate([
            'evaluation_id' => 'required|exists:evaluations,id',
        ]);

        DB::table('procurement_evaluations')
            ->updateOrInsert(
                ['procurement_id' => $procurement->id],
                ['evaluation_id' => $request->evaluation_id]
            );

        return redirect()
            ->route('procurements.show', $procurement)
            ->with('success', 'Evaluation assigned to procurement.');
    }
}