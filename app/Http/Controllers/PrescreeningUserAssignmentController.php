<?php
namespace App\Http\Controllers;

use App\Mail\PrescreeningAssigned;
use App\Models\FormSubmission;
use App\Models\Procurement;
use App\Models\User;
use App\Models\PrescreeningAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Procurement\Concerns\GovernanceScope;

class PrescreeningUserAssignmentController extends Controller
{
    use GovernanceScope;

    /**
     * INDEX
     * Accordion view: each procurement + assigned users
     */
    public function index()
    {
        $scopedNodeIds = $this->scopedNodeIds();
        if ($scopedNodeIds !== null && empty($scopedNodeIds)) {
            abort(403, 'You do not have access to procurements.');
        }

        $procurements = Procurement::with('prescreeningUsers')
            ->when($scopedNodeIds !== null, function ($query) use ($scopedNodeIds) {
                $query->whereIn('governance_node_id', $scopedNodeIds)
                    ->whereNotNull('governance_node_id');
            })
            ->latest()
            ->get();

        return view(
            'prescreening.assignments.index',
            compact('procurements')
        );
    }

    /**
     * EDIT
     * Assign users to one procurement
     */
    public function edit(Procurement $procurement)
    {
        $this->assertProcurementInScope($procurement);
        $users = User::orderBy('name')->get();
        $submissions = $procurement->submissions()
            ->orderByDesc('created_at')
            ->get();

        $assignedProcurementUserId = $procurement->prescreeningUsers->first()?->id;
        $assignedSubmission = $submissions
            ->firstWhere('assigned_prescreener_id', '!=', null);

        return view(
            'prescreening.assignments.edit',
            compact(
                'procurement',
                'users',
                'submissions',
                'assignedProcurementUserId',
                'assignedSubmission'
            )
        );
    }

    /**
     * STORE
     * Save user assignments
     */
    public function store(Request $request, Procurement $procurement)
    {
        $this->assertProcurementInScope($procurement);
        $request->validate([
            'assignment_type' => 'required|in:procurement,submission',
            'user_id' => 'required|exists:users,id',
            'submission_id' => 'required_if:assignment_type,submission|nullable|exists:form_submissions,id',
        ]);

        // Reset assignments (enforce single prescreener)
        PrescreeningAssignment::where('procurement_id', $procurement->id)->delete();
        FormSubmission::where('procurement_id', $procurement->id)
            ->update(['assigned_prescreener_id' => null]);

        $assignee = User::findOrFail($request->user_id);

        if ($request->assignment_type === 'procurement') {
            PrescreeningAssignment::create([
                'procurement_id' => $procurement->id,
                'user_id'        => $assignee->id,
                'assigned_by'    => auth()->id(),
                'assigned_at'    => now(),
            ]);
        }

        if ($request->assignment_type === 'submission' && $request->submission_id) {
            FormSubmission::where('id', $request->submission_id)
                ->where('procurement_id', $procurement->id)
                ->update(['assigned_prescreener_id' => $assignee->id]);
        }

        if ($assignee->email) {
            Mail::to($assignee->email)->send(
                new PrescreeningAssigned($assignee, $procurement)
            );
        }

        return redirect()
            ->route('prescreening.assignments.index')
            ->with('success', 'Prescreening user assigned successfully.');
    }

    /**
     * My Assignments (for prescreeners)
     */
    public function myAssignments()
    {
        $userId = auth()->id();

        $scopedNodeIds = $this->scopedNodeIds();
        $procurements = Procurement::whereHas('prescreeningAssignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when($scopedNodeIds !== null, function ($query) use ($scopedNodeIds) {
                $query->whereIn('governance_node_id', $scopedNodeIds)
                    ->whereNotNull('governance_node_id');
            })
            ->withCount('submissions')
            ->orderByDesc('created_at')
            ->get();

        $procurementSubmissions = FormSubmission::with(['procurement', 'prescreeningResult'])
            ->whereIn('procurement_id', $procurements->pluck('id'))
            ->orderByDesc('created_at')
            ->get();

        $submissions = FormSubmission::with(['procurement', 'prescreeningResult'])
            ->where('assigned_prescreener_id', $userId)
            ->when($scopedNodeIds !== null, function ($query) use ($scopedNodeIds) {
                $query->whereHas('procurement', function ($proc) use ($scopedNodeIds) {
                    $proc->whereIn('governance_node_id', $scopedNodeIds)
                        ->whereNotNull('governance_node_id');
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('prescreening.assignments.my', compact(
            'procurements',
            'procurementSubmissions',
            'submissions'
        ));
    }
}
