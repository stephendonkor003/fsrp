<?php

namespace App\Http\Controllers;

use App\Models\EvaluationSubmission;
use App\Models\Procurement;
use Barryvdh\DomPDF\Facade\Pdf;

class EvaluationPanelPdfController extends Controller
{
    /* ===============================
     | SINGLE EVALUATOR PDF
     =============================== */
    public function single(EvaluationSubmission $submission)
    {
        $submission->load([
            'evaluation.sections.criteria',
            'criteriaScores.criteria',
            'sectionScores.section',
            'evaluator',
            'procurement',
            'applicant.submitter'
        ]);

        $pdf = Pdf::loadView(
            'evaluations.panel.pdf.single',
            compact('submission')
        );

        return $pdf->download(
            'evaluation-' . $submission->id . '.pdf'
        );
    }

    /* ===============================
     | BULK PDF PER PROCUREMENT
     =============================== */
     public function bulk(Procurement $procurement)
    {
        $submissions = EvaluationSubmission::with([
                'evaluation.sections.criteria',
                'criteriaScores.criteria',
                'sectionScores.section',
                'evaluator',
                'applicant.submitter',
            ])
            ->where('procurement_id', $procurement->id)
            ->whereNotNull('submitted_at')
            ->get();

        abort_if($submissions->isEmpty(), 404, 'No evaluations found');

        return Pdf::loadView(
                'evaluations.panel.pdf.bulk',
                compact('procurement', 'submissions')
            )
            ->setPaper('a4', 'portrait')
            ->download(
                'panel-evaluations-' . $procurement->id . '.pdf'
            );
    }
}