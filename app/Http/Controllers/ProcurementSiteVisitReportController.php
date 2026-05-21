<?php

namespace App\Http\Controllers;

use App\Models\{
    Procurement,
    SiteVisit
};

class ProcurementSiteVisitReportController extends Controller
{
    public function show(Procurement $procurement)
    {
        // Admin / oversight only
        if (!auth()->user()->can('site_visits.approve')) {
            abort(403, 'Unauthorized');
        }

        $siteVisits = SiteVisit::with([
            'submission',
            'assignment.user',
            'group.leader',
            'group.members.user',
            'observations.media'
        ])
        ->where('procurement_id', $procurement->id)
        ->orderBy('visit_date')
        ->get();

        return view(
            'site-visits.reports.comprehensive', // ✅ UPDATED PATH
            compact('procurement', 'siteVisits')
        );
    }


    public function index()
{
    if (!auth()->user()->can('site_visits.approve')) {
        abort(403);
    }

    $procurements = \App\Models\Procurement::orderBy('title')->get();

    return view(
        'site-visits.reports.index',
        compact('procurements')
    );
}

}