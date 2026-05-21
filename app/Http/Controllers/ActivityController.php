<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Program;
use App\Models\ActivityAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display all activities (with project & program info)
     */
    public function index(Request $request)
{
    $search = $request->search;

    $scopedNodeIds = $this->scopedNodeIds();
    if ($scopedNodeIds !== null && empty($scopedNodeIds)) {
        abort(403, 'You do not have access to activities.');
    }

    $programs = Program::with([
        'projects.activities.allocations' => function ($q) {
            $q->orderBy('year', 'asc');
        }
    ])
    ->when($scopedNodeIds !== null, function ($q) use ($scopedNodeIds) {
        $q->whereIn('governance_node_id', $scopedNodeIds)
          ->whereNotNull('governance_node_id');
    })
    ->when($search, function ($q) use ($search) {
        $q->where(function ($q2) use ($search) {
            $q2->where('name', 'like', "%$search%")
              ->orWhereHas('projects', function ($p) use ($search) {
                  $p->where('name', 'like', "%$search%")
                    ->orWhere('project_id', 'like', "%$search%");
              })
              ->orWhereHas('projects.activities', function ($a) use ($search) {
                  $a->where('name', 'like', "%$search%");
              });
        });
    })
    ->orderBy('name')
    ->get();

    return view('activities.index', compact('programs', 'search'));
}


    /**
     * Show create activity form
     */
    public function create(Project $project)
    {
        $this->assertProjectInScope($project);
        $project->load(['program', 'sector']);
        return view('activities.create', compact('project'));
    }


    /**
     * Store a new activity
     */
 public function store(Request $request)
{
    $request->validate([
        'project_id' => 'required|exists:myb_projects,id',
        'name'       => 'required|string|max:255',
        'expected_outcome_type' => 'required|in:percentage,text',
        'expected_outcome_percentage' => 'nullable|numeric|min:0|max:100',
        'expected_outcome_text' => 'nullable|string|max:2000',
    ]);

    $project = Project::findOrFail($request->project_id);
    $this->assertProjectInScope($project);

    $expectedOutcomeValue = $request->expected_outcome_type === 'percentage'
        ? (string) ($request->expected_outcome_percentage ?? '')
        : ($request->expected_outcome_text ?? '');

    if ($request->expected_outcome_type === 'percentage' && $expectedOutcomeValue === '') {
        return back()->withErrors(['expected_outcome_percentage' => 'Expected outcome percentage is required.'])->withInput();
    }

    if ($request->expected_outcome_type === 'text' && $expectedOutcomeValue === '') {
        return back()->withErrors(['expected_outcome_text' => 'Expected outcome description is required.'])->withInput();
    }

    // Create Activity
    $activity = Activity::create([
        'project_id'  => $request->project_id,
        'governance_node_id' => $project->governance_node_id,
        'name'        => $request->name,
        'description' => $request->description,
        'expected_outcome_type' => $request->expected_outcome_type,
        'expected_outcome_value' => $expectedOutcomeValue,
        'created_by'  => auth()->id(),
    ]);

    /**
     * Save Allocation Amounts Submitted from the Blade
     * The Blade sends allocations[year] => amount
     */
    if ($request->has('allocations')) {

        foreach ($request->allocations as $year => $amount) {

            ActivityAllocation::create([
                'activity_id' => $activity->id,
                'year'        => $year,
                'amount'      => $amount !== null ? floatval($amount) : 0,
            ]);
        }

    } else {

        // Fallback — should never happen with your Blade
        foreach ($project->years() as $year) {
            ActivityAllocation::create([
                'activity_id' => $activity->id,
                'year'        => $year,
                'amount'      => 0,
            ]);
        }
    }

    return redirect()->route('budget.projects.show', $project->id)
                     ->with('success', 'Activity created successfully.');
}

    /**
     * Edit Activity Allocations
     */
    public function editAllocations($id)
    {
        $activity = Activity::with('allocations', 'project.program')->findOrFail($id);
        $this->assertActivityInScope($activity);

        return view('activities.edit_allocations', compact('activity'));
    }

    /**
     * Update Activity Allocations
     */
    public function updateAllocations(Request $request, $id)
    {
        $activity = Activity::with('project')->findOrFail($id);
        $this->assertActivityInScope($activity);

        foreach ($request->allocations as $allocId => $amount) {
            ActivityAllocation::where('id', $allocId)->update([
                'amount' => $amount ?? 0
            ]);
        }

        return back()->with('success', 'Activity allocations updated successfully.');
    }


    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'expected_outcome_type' => 'required|in:percentage,text',
        'expected_outcome_percentage' => 'nullable|numeric|min:0|max:100',
        'expected_outcome_text' => 'nullable|string|max:2000',
    ]);

    $activity = Activity::findOrFail($id);
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

    $activity->update([
        'name'        => $request->name,
        'description' => $request->description,
        'expected_outcome_type' => $request->expected_outcome_type,
        'expected_outcome_value' => $expectedOutcomeValue,
    ]);

    return redirect()
        ->route('budget.activities.index')
        ->with('success', 'Activity updated successfully.');
}


 public function show($id)
{
    $activity = Activity::with([
        'project.program',
        'project.sector',
        'allocations' => function ($q) {
            $q->orderBy('year', 'asc');
        }
    ])->findOrFail($id);
    $this->assertActivityInScope($activity);

    $project = $activity->project;

    // Useful calculations for the blade
    $totalAllocation = $activity->allocations->sum('amount');
    $projectBudget   = $project->total_budget;
    $remainingBudget = $projectBudget - $totalAllocation;
    $percentageUsed  = $projectBudget > 0
                        ? ($totalAllocation / $projectBudget) * 100
                        : 0;

    return view('activities.show', compact(
        'activity',
        'project',
        'totalAllocation',
        'remainingBudget',
        'percentageUsed'
    ));
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

    private function assertProjectInScope(Project $project): void
    {
        $scopedNodeIds = $this->scopedNodeIds();
        if ($scopedNodeIds === null) {
            return;
        }

        if (!$project->governance_node_id || !in_array($project->governance_node_id, $scopedNodeIds, true)) {
            abort(403, 'You do not have access to this project.');
        }
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


}
