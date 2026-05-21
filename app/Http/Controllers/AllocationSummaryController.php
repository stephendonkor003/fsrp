<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Project;
use App\Models\Activity;
use App\Models\SubActivity;
use Illuminate\Http\Request;

class AllocationSummaryController extends Controller
{
    /**
     * =========================================================================
     *  BUDGET DASHBOARD
     * =========================================================================
     *  High-level KPIs + Program/Project/Activity allocation summaries
     */
    public function dashboard()
    {
        // Load entire budget hierarchy
        $programs = Program::with([
            'projects.activities.allocations',
            'projects.activities.subActivities.allocations'
        ])->get();

        // KPI totals
        $totalPrograms = $programs->count();
        $totalProjects = Project::count();
        $totalActivities = Activity::count();
        $totalSubActivities = SubActivity::count();

        // Total budget from all projects
        $totalBudget = Project::sum('total_budget');

        // Total allocation for all activities
        $totalAllocated = Activity::with('allocations')->get()
            ->sum(fn($a) => $a->allocations->sum('amount'));

        // Total allocation for all sub-activities
        $totalSubAllocated = SubActivity::with('allocations')->get()
            ->sum(fn($sa) => $sa->allocations->sum('amount'));

        // Variance remaining (budget - allocations)
        $remainingBudget = $totalBudget - ($totalAllocated + $totalSubAllocated);

        return view('reports.budget_dashboard', compact(
            'programs',
            'totalPrograms',
            'totalProjects',
            'totalActivities',
            'totalSubActivities',
            'totalBudget',
            'totalAllocated',
            'totalSubAllocated',
            'remainingBudget'
        ));
    }


    /**
     * =========================================================================
     *  EXECUTIVE REPORTS
     * =========================================================================
     *  Rankings, comparisons, variance analysis, performance summaries
     */
    public function executiveReports()
    {
        // Load hierarchy for reporting
        $programs = Program::with([
            'projects.activities.allocations',
            'projects.activities.subActivities.allocations'
        ])->get();

        // Rank projects by total allocated amount
        $projectRankings = Project::with('activities.allocations')
            ->get()
            ->map(function ($project) {
                return [
                    'project' => $project,
                    'allocated' => $project->activities->sum(
                        fn($a) => $a->allocations->sum('amount')
                    ),
                ];
            })
            ->sortByDesc('allocated')
            ->values();

        // Rank activities by funding level
        $activityRankings = Activity::with('allocations', 'project')
            ->get()
            ->map(function ($activity) {
                return [
                    'activity' => $activity,
                    'project' => $activity->project,
                    'allocated' => $activity->allocations->sum('amount'),
                ];
            })
            ->sortByDesc('allocated')
            ->values();

        // Compare sub-activity allocations
        $subActivityRankings = SubActivity::with('allocations', 'activity.project')
            ->get()
            ->map(function ($sub) {
                return [
                    'sub' => $sub,
                    'activity' => $sub->activity,
                    'project' => $sub->activity->project,
                    'allocated' => $sub->allocations->sum('amount'),
                ];
            })
            ->sortByDesc('allocated')
            ->values();

        return view('reports.executive_summary', compact(
            'programs',
            'projectRankings',
            'activityRankings',
            'subActivityRankings'
        ));
    }
}
