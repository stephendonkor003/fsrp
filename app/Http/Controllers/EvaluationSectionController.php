<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationSection;
use Illuminate\Http\Request;

class EvaluationSectionController extends Controller
{
    /**
     * Store a new evaluation section
     */
    public function store(Request $request, Evaluation $evaluation)
    {
        if ($evaluation->status !== 'draft') {
            return back()->with('error', 'Cannot modify sections once evaluation is active.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $evaluation->sections()->create([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Evaluation section added successfully.');
    }

    /**
     * Update a section
     */
    public function update(Request $request, EvaluationSection $section)
    {
        if ($section->evaluation->status !== 'draft') {
            return back()->with('error', 'Cannot modify sections once evaluation is active.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section->update($request->only('name', 'description'));

        return back()->with('success', 'Section updated successfully.');
    }

    /**
     * Delete a section
     */
    public function destroy(EvaluationSection $section)
    {
        if ($section->evaluation->status !== 'draft') {
            return back()->with('error', 'Cannot delete sections once evaluation is active.');
        }

        $section->delete();

        return back()->with('success', 'Section removed successfully.');
    }
}
