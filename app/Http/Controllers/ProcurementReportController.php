<?php

namespace App\Http\Controllers;

use App\Models\{
    Procurement,
    FormSubmission,
    SiteVisit
};

class ProcurementReportController extends Controller
{
    public function show(Procurement $procurement)
    {
        // Permission gate
        if (!auth()->user()->can('site_visits.approve')) {
            abort(403, 'Unauthorized');
        }

        $submissions = FormSubmission::with([
            'submitter',
            'evaluationSubmissions.evaluator',
            'siteVisits.group.leader',
            'siteVisits.group.members.user',
            'siteVisits.assignment.user',
            'siteVisits.observations'
        ])
        ->where('procurement_id', $procurement->id)
        ->get();

        return view(
            'procurements.reports.evaluation-360',
            compact('procurement', 'submissions')
        );
    }
}