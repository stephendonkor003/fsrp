<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Evaluation;
use App\Models\PrescreeningCriterion;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\ConsortiumReportMail;

class PublicCheckController extends Controller
{
    /**
     * Display the public search page.
     */
    public function index()
    {
        return view('public.check.index');
    }

    /**
     * Handle consortium search and display results.
     */
 public function search(Request $request)
{
    $request->validate([
        'consortium_name' => 'required|string|max:255',
    ]);

    // 🔹 Search by consortium name (case-insensitive partial match)
    $consortium = Applicant::whereRaw('LOWER(consortium_name) LIKE ?', [
            '%' . strtolower(trim($request->consortium_name)) . '%'
        ])
        ->with(['prescreeningCriteria', 'evaluations.evaluator'])
        ->first();

    // 🔸 If no consortium found → show "no result" page
    if (!$consortium) {
        return view('public.check.noresult');
    }

    // 🔹 Get prescreening and evaluations
    $prescreening = $consortium->prescreeningCriteria;
    $evaluations  = $consortium->evaluations;

    // 🔹 Prepare evaluation data
    $analysis = [];
    $proposalScores = [];
    $personnelScores = [];
    $budgetScores = [];

    foreach ($evaluations as $evaluation) {
        $proposalScores[]  = $evaluation->proposal_score ?? 0;
        $personnelScores[] = $evaluation->personnel_score ?? 0;
        $budgetScores[]    = $evaluation->budget_score ?? 0;

        $analysis[] = [
            'evaluator' => $evaluation->evaluator->name ?? 'N/A',
            'proposal'  => $evaluation->proposal_score ?? 0,
            'personnel' => $evaluation->personnel_score ?? 0,
            'budget'    => $evaluation->budget_score ?? 0,
            'total'     => $evaluation->total_score ?? 0,
        ];
    }

    // 🔹 Compute summary averages
    $summary = [
        'avg_proposal'  => count($proposalScores) ? round(array_sum($proposalScores) / count($proposalScores), 2) : 0,
        'avg_personnel' => count($personnelScores) ? round(array_sum($personnelScores) / count($personnelScores), 2) : 0,
        'avg_budget'    => count($budgetScores) ? round(array_sum($budgetScores) / count($budgetScores), 2) : 0,
        'avg_total'     => count($evaluations) ? round($evaluations->avg('total_score'), 2) : 0,
    ];

    // 🔹 Render the result page with all data
    return view('public.check.result', compact('consortium', 'prescreening', 'analysis', 'summary'));
}


    /**
     * Handle request to send the consortium report via email.
     */
    public function sendEmail(Request $request)
    {
        $request->validate([
            'email'          => 'required|email',
            'consortium_id'  => 'required|integer|exists:applicants,id',
        ]);

        // 🔸 Load consortium + relations
        $consortium = Applicant::with(['prescreeningCriteria', 'evaluations.evaluator'])->findOrFail($request->consortium_id);

        // 🔒 Security check — only allow original submission email
        if (strtolower(trim($consortium->email)) !== strtolower(trim($request->email))) {
            return back()->with('error', 'Email does not match the one used in the original submission.');
        }

        // 🔸 Collect prescreening & evaluation data
        $prescreening = $consortium->prescreeningCriteria;
        $evaluations  = $consortium->evaluations;

        $analysis = [];
        foreach ($evaluations as $evaluation) {
            $analysis[] = [
                'evaluator' => $evaluation->evaluator->name ?? 'N/A',
                'proposal'  => $evaluation->proposal_score ?? 0,
                'personnel' => $evaluation->personnel_score ?? 0,
                'budget'    => $evaluation->budget_score ?? 0,
                'total'     => $evaluation->total_score ?? 0,
            ];
        }

        $summary = [
            'avg_proposal'  => round($evaluations->avg('proposal_score'), 2),
            'avg_personnel' => round($evaluations->avg('personnel_score'), 2),
            'avg_budget'    => round($evaluations->avg('budget_score'), 2),
            'avg_total'     => round($evaluations->avg('total_score'), 2),
        ];

        // 🔸 Generate PDF
        $pdf = Pdf::loadView('emails.consortium_report_pdf', compact('consortium', 'prescreening', 'analysis', 'summary'))
            ->setPaper('a4', 'portrait');

        // 🔸 Send via Mailable
        Mail::to($consortium->email)->send(new ConsortiumReportMail($consortium, $pdf));

        // 🔸 Return success message
        return back()->with('success', '✅ Your detailed evaluation report has been sent to your registered email.');
    }
}
