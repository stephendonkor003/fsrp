<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ApprovedWorkPlan;
use App\Models\FsrpComponent;
use App\Models\FsrpGrievance;
use App\Models\FsrpSafeguardScreening;
use App\Models\FsrpStakeholderEngagement;
use App\Models\ProcurementPlan;
use App\Models\SubActivity;
use App\Models\User;
use Illuminate\Http\Request;

class FsrpSafeguardsController extends Controller
{
    public function index()
    {
        $fsrpComponents = FsrpComponent::with(['subcomponents' => fn ($query) => $query->active()->orderBy('sort_order')->orderBy('code')])
            ->active()
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $screenings = FsrpSafeguardScreening::with([
            'component:id,code,name',
            'subcomponent:id,code,name',
            'activity:id,name',
            'subActivity:id,name',
            'procurementPlan:id,procurement_code,title',
            'workPlan:id,awp_code,title',
            'screener:id,name',
        ])->latest()->get();

        $engagements = FsrpStakeholderEngagement::with([
            'component:id,code,name',
            'subcomponent:id,code,name',
            'creator:id,name',
        ])->latest()->get();

        $grievances = FsrpGrievance::with([
            'component:id,code,name',
            'subcomponent:id,code,name',
            'assignee:id,name',
        ])->latest()->get();

        $summary = [
            'screenings' => $screenings->count(),
            'high_risk_screenings' => $screenings->whereIn('risk_level', ['substantial', 'high'])->count(),
            'engagements_open' => $engagements->where('status', 'open')->count(),
            'grievances_open' => $grievances->whereIn('status', ['open', 'assigned', 'investigating'])->count(),
        ];

        return view('fsrp.safeguards.index', [
            'fsrpComponents' => $fsrpComponents,
            'screenings' => $screenings,
            'engagements' => $engagements,
            'grievances' => $grievances,
            'summary' => $summary,
            'activities' => Activity::orderBy('name')->get(['id', 'name']),
            'subActivities' => SubActivity::orderBy('name')->get(['id', 'name']),
            'procurementPlans' => ProcurementPlan::orderByDesc('created_at')->limit(200)->get(['id', 'procurement_code', 'title']),
            'workPlans' => ApprovedWorkPlan::orderByDesc('created_at')->limit(200)->get(['id', 'awp_code', 'title']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeScreening(Request $request)
    {
        $validated = $this->validateScreening($request);
        $validated['screening_code'] = FsrpSafeguardScreening::nextCode();
        $validated['created_by'] = auth()->id();

        FsrpSafeguardScreening::create($validated);

        return back()->with('success', 'Safeguard screening recorded.');
    }

    public function updateScreening(Request $request, FsrpSafeguardScreening $screening)
    {
        $screening->update($this->validateScreening($request));

        return back()->with('success', 'Safeguard screening updated.');
    }

    public function storeEngagement(Request $request)
    {
        $validated = $this->validateEngagement($request);
        $validated['engagement_code'] = FsrpStakeholderEngagement::nextCode();
        $validated['created_by'] = auth()->id();

        FsrpStakeholderEngagement::create($validated);

        return back()->with('success', 'Stakeholder engagement recorded.');
    }

    public function updateEngagement(Request $request, FsrpStakeholderEngagement $engagement)
    {
        $engagement->update($this->validateEngagement($request));

        return back()->with('success', 'Stakeholder engagement updated.');
    }

    public function storeGrievance(Request $request)
    {
        $validated = $this->validateGrievance($request);
        $validated['case_code'] = FsrpGrievance::nextCode();
        $validated['created_by'] = auth()->id();
        if (($validated['status'] ?? null) === 'closed' && empty($validated['closed_at'])) {
            $validated['closed_at'] = now();
        }

        FsrpGrievance::create($validated);

        return back()->with('success', 'GRM case recorded.');
    }

    public function updateGrievance(Request $request, FsrpGrievance $grievance)
    {
        $validated = $this->validateGrievance($request);
        if (($validated['status'] ?? null) === 'closed' && empty($validated['closed_at'])) {
            $validated['closed_at'] = now();
        }

        $grievance->update($validated);

        return back()->with('success', 'GRM case updated.');
    }

    private function validateScreening(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'fsrp_component_id' => 'nullable|exists:fsrp_components,id',
            'fsrp_subcomponent_id' => 'nullable|exists:fsrp_subcomponents,id',
            'activity_id' => 'nullable|exists:myb_activities,id',
            'sub_activity_id' => 'nullable|exists:myb_sub_activities,id',
            'procurement_plan_id' => 'nullable|exists:myb_procurement_plans,id',
            'approved_work_plan_id' => 'nullable|exists:approved_work_plans,id',
            'risk_level' => 'required|in:low,moderate,substantial,high',
            'screening_status' => 'required|in:draft,screened,mitigation_required,cleared,closed',
            'screened_on' => 'nullable|date',
            'screened_by' => 'nullable|exists:users,id',
            'environmental_risks' => 'nullable|string',
            'social_risks' => 'nullable|string',
            'mitigation_measures' => 'nullable|string',
            'evidence_reference' => 'nullable|string',
            'next_review_due_on' => 'nullable|date',
        ]);
    }

    private function validateEngagement(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'fsrp_component_id' => 'nullable|exists:fsrp_components,id',
            'fsrp_subcomponent_id' => 'nullable|exists:fsrp_subcomponents,id',
            'engagement_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'stakeholder_group' => 'nullable|string|max:255',
            'participants_count' => 'nullable|integer|min:0',
            'summary' => 'nullable|string',
            'commitments_made' => 'nullable|string',
            'follow_up_actions' => 'nullable|string',
            'follow_up_due_on' => 'nullable|date',
            'status' => 'required|in:open,in_progress,completed,overdue',
        ]);
    }

    private function validateGrievance(Request $request): array
    {
        return $request->validate([
            'complainant_name' => 'nullable|string|max:255',
            'complainant_contact' => 'nullable|string|max:255',
            'category' => 'required|in:general,environmental,social,procurement,labor,gbv,other',
            'priority' => 'required|in:low,normal,high,critical',
            'status' => 'required|in:open,assigned,investigating,resolved,closed',
            'fsrp_component_id' => 'nullable|exists:fsrp_components,id',
            'fsrp_subcomponent_id' => 'nullable|exists:fsrp_subcomponents,id',
            'received_on' => 'nullable|date',
            'description' => 'required|string',
            'resolution_actions' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_on' => 'nullable|date',
            'closed_at' => 'nullable|date',
            'closure_notes' => 'nullable|string',
        ]);
    }
}
