<?php

namespace App\Http\Controllers\MemberState;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberStateDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->loadMissing('memberState');

        return view('member-state.dashboard', [
            'memberState' => $user->memberState,
            'portalCards' => [
                [
                    'number' => '01',
                    'title' => 'Submit Information',
                    'eyebrow' => 'Country reporting',
                    'description' => 'Open the complete Sections A–R reporting workspace and submit programme information.',
                    'route' => 'member-state.reporting.index',
                    'image' => 'admin/assets/images/member-state/reporting-sections/section-a-executive-summary.png',
                    'keywords' => 'submit information data reporting forms sections modules',
                    'theme' => 'emerald',
                ],
                [
                    'number' => '02',
                    'title' => 'View Performance',
                    'eyebrow' => 'Results and trends',
                    'description' => 'Review country performance, progress trends, peer comparisons, and reported results.',
                    'route' => 'member-state.comparisons.index',
                    'image' => 'admin/assets/images/member-state/reporting-sections/section-b-pdo-results.png',
                    'keywords' => 'view performance results trends indicators comparison progress',
                    'theme' => 'teal',
                ],
                [
                    'number' => '03',
                    'title' => 'Check Notifications',
                    'eyebrow' => 'Messages and responses',
                    'description' => 'Check official communications, AU responses, attachments, and status updates.',
                    'route' => 'member-state.communications.index',
                    'image' => 'admin/assets/images/member-state/reporting-sections/section-o-citizen-engagement.png',
                    'keywords' => 'check notifications messages alerts communications responses updates',
                    'theme' => 'amber',
                ],
                [
                    'number' => '04',
                    'title' => 'Documents and Raw Data',
                    'eyebrow' => 'Country records',
                    'description' => 'Access national records, supporting evidence, review status, and raw data submissions.',
                    'route' => 'member-state.national-data.index',
                    'image' => 'admin/assets/images/member-state/reporting-sections/section-r-submission-checklist.png',
                    'keywords' => 'documents raw data files evidence records downloads national',
                    'theme' => 'olive',
                ],
            ],
        ]);
    }
}
