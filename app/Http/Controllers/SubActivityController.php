<?php

 namespace App\Http\Controllers;

use App\Models\SubActivity;
use App\Models\Activity;
use App\Models\SubActivityAllocation;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubActivityController extends Controller
{

 public function index(Request $request)
{
    $search = $request->search;

    $scopedNodeIds = $this->scopedNodeIds();
    if ($scopedNodeIds !== null && empty($scopedNodeIds)) {
        abort(403, 'You do not have access to sub-activities.');
    }

    $programs = Program::with([
        'projects.activities.subActivities.allocations' => function ($q) {
            $q->orderBy('year', 'asc');
        },
        'projects.activities.allocations'
    ])
    ->when($scopedNodeIds !== null, function ($q) use ($scopedNodeIds) {
        $q->whereIn('governance_node_id', $scopedNodeIds)
          ->whereNotNull('governance_node_id');
    })
    ->when($search, function ($q) use ($search) {
        $q->where(function ($q2) use ($search) {
            $q2->where('name', 'like', "%$search%")
              ->orWhereHas('projects', function ($p) use ($search) {
                  $p->where('name', 'like', "%$search%");
              })
              ->orWhereHas('projects.activities', function ($a) use ($search) {
                  $a->where('name', 'like', "%$search%");
              })
              ->orWhereHas('projects.activities.subActivities', function ($s) use ($search) {
                  $s->where('name', 'like', "%$search%");
              });
        });
    })
    ->orderBy('name')
    ->get();

    return view('subactivities.index', compact('programs', 'search'));
}


public function create(Activity $activity)
{
    $this->assertActivityInScope($activity);
    $activity->load('project.program', 'allocations');

    return view('subactivities.create', compact('activity'));
}



    public function store(Request $request)
{
    $request->validate([
        'activity_id' => 'required|exists:myb_activities,id',
        'name' => 'required|string|max:255',
        'expected_outcome_type' => 'required|in:percentage,text',
        'expected_outcome_percentage' => 'nullable|numeric|min:0|max:100',
        'expected_outcome_text' => 'nullable|string|max:2000',
    ]);

    $activity = Activity::findOrFail($request->activity_id);
    $this->assertActivityInScope($activity);

    $expectedOutcomeValue = $request->expected_outcome_type === 'percentage'
        ? (string) ($request->expected_outcome_percentage ?? '')
        : ($request->expected_outcome_text ?? '');

    if ($request->expected_outcome_type === 'percentage' && $expectedOutcomeValue === '') {
        return back()->withErrors(['expected_outcome_percentage' => 'Expected outcome percentage is required.'])->withInput();
    }

    if ($request->expected_outcome_type === 'text' && $expectedOutcomeValue === '') {
        return back()->withErrors(['expected_outcome_text' => 'Expected outcome description is required.'])->withInput();
    }

    // Create Sub-Activity
    $sub = SubActivity::create([
        'activity_id' => $request->activity_id,
        'governance_node_id' => $activity->governance_node_id,
        'name' => $request->name,
        'description' => $request->description,
        'expected_outcome_type' => $request->expected_outcome_type,
        'expected_outcome_value' => $expectedOutcomeValue,
        'created_by' => auth()->id(),
    ]);

    // Save allocations from the form
    foreach ($request->allocations as $year => $amount) {
        SubActivityAllocation::create([
            'sub_activity_id' => $sub->id,
            'year' => $year,
            'amount' => $amount ?? 0,
        ]);
    }

    // return redirect()
    //     ->route('activities.show', $activity->id)
    //     ->with('success', 'Sub-Activity created successfully.');
    return redirect()
    ->route('budget.activities.show', $activity->id)
    ->with('success', 'Sub-Activity created successfully.');

}


    public function editAllocations($id)
{
    $sub = SubActivity::with('allocations', 'activity.project.program')->findOrFail($id);
    $this->assertSubActivityInScope($sub);
    return view('subactivities.edit_allocations', compact('sub'));
}

public function updateAllocations(Request $request, $id)
{
    $sub = SubActivity::with('activity.project')->findOrFail($id);
    $this->assertSubActivityInScope($sub);

    foreach ($request->allocations as $allocId => $amount) {
        SubActivityAllocation::where('id', $allocId)->update(['amount' => $amount ?? 0]);
    }

    return back()->with('success', 'Sub-Activity allocations updated successfully.');
}

public function destroy($id)
{
    $sub = SubActivity::findOrFail($id);
    $this->assertSubActivityInScope($sub);

    // Optional → delete allocations first if foreign key constraints apply
    $sub->allocations()->delete();

    $sub->delete();

    return back()->with('success', 'Sub-Activity deleted successfully.');
}

    private function scopedNodeIds(): ?array
    {
        $currentUser = Auth::user();

        if (!$currentUser || $currentUser->isAdmin()) {
            return null;
        }

        if (!$currentUser->governance_node_id) {
            return [];
        }

        return [$currentUser->governance_node_id];
    }

    private function assertActivityInScope(Activity $activity): void
    {
        $scopedNodeIds = $this->scopedNodeIds();
        if ($scopedNodeIds === null) {
            return;
        }

        $nodeId = $activity->governance_node_id ?? $activity->project?->governance_node_id;
        if (!$nodeId || !in_array($nodeId, $scopedNodeIds, true)) {
            abort(403, 'You do not have access to this activity.');
        }
    }

    private function assertSubActivityInScope(SubActivity $sub): void
    {
        $scopedNodeIds = $this->scopedNodeIds();
        if ($scopedNodeIds === null) {
            return;
        }

        $nodeId = $sub->governance_node_id ?? $sub->activity?->governance_node_id;
        if (!$nodeId || !in_array($nodeId, $scopedNodeIds, true)) {
            abort(403, 'You do not have access to this sub-activity.');
        }
    }


}
