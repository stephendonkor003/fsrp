<?php

namespace App\Http\Controllers\MemberState;

use App\Http\Controllers\Controller;
use App\Models\MemberStateReportingCycle;
use App\Models\MemberStateReportSubmission;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReportingSectionController extends Controller
{
    private const FREQUENCY_OPTIONS = [
        'QUARTERLY' => [
            'label' => 'Quarterly',
            'short_label' => 'Quarterly report',
            'description' => 'Report once for the quarter opened by the M&E team.',
            'icon' => 'feather-calendar',
        ],
        'SEMI_ANNUAL' => [
            'label' => 'Semi-Annual',
            'short_label' => 'Six-month report',
            'description' => 'Report once for the active six-month period.',
            'icon' => 'feather-columns',
        ],
        'ANNUAL' => [
            'label' => 'Annual',
            'short_label' => 'Annual report',
            'description' => 'Report once for the annual period opened by M&E.',
            'icon' => 'feather-calendar',
        ],
    ];

    public function index(Request $request)
    {
        $user = $request->user()->loadMissing('memberState');
        $memberState = $user->memberState;

        abort_unless($memberState, 403, 'A Member State must be assigned to your account.');

        $openCycles = $this->openCyclesByFrequency();
        $cycleIds = $openCycles->pluck('id');
        $submissions = MemberStateReportSubmission::query()
            ->where('member_state_id', $memberState->id)
            ->whereIn('reporting_cycle_id', $cycleIds)
            ->get()
            ->keyBy('reporting_cycle_id');

        $selectedSubmission = $this->selectedSubmission($request, $memberState->id);
        $frequencyOptions = collect(self::FREQUENCY_OPTIONS)
            ->map(function (array $metadata, string $code) use ($openCycles, $submissions): array {
                $cycle = $openCycles->get($code);
                $submission = $cycle ? $submissions->get($cycle->id) : null;

                return array_merge($metadata, [
                    'code' => $code,
                    'cycle' => $cycle,
                    'submission' => $submission,
                    'available' => (bool) $cycle,
                ]);
            })
            ->values();

        return view('member-state.reporting-index', [
            'memberState' => $memberState,
            'reportingSections' => config('member_state_reporting.sections'),
            'frequencyOptions' => $frequencyOptions,
            'selectedSubmission' => $selectedSubmission,
        ]);
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'reporting_cycle_id' => [
                'required',
                'uuid',
                Rule::exists('me_member_state_reporting_cycles', 'id'),
            ],
        ]);

        $user = $request->user()->loadMissing('memberState');
        $memberState = $user->memberState;

        abort_unless($memberState, 403, 'A Member State must be assigned to your account.');

        $cycle = MemberStateReportingCycle::query()
            ->with('reportingFrequency')
            ->findOrFail($validated['reporting_cycle_id']);

        abort_unless(
            in_array($cycle->reportingFrequency?->code, array_keys(self::FREQUENCY_OPTIONS), true),
            422,
            'This reporting frequency is not available in the Member State portal.'
        );

        $existingSubmission = MemberStateReportSubmission::query()
            ->where('member_state_id', $memberState->id)
            ->where('reporting_cycle_id', $cycle->id)
            ->first();

        if (! $existingSubmission && ! $cycle->isAcceptingSubmissions()) {
            return back()->withErrors([
                'reporting_cycle_id' => 'This reporting period is not currently open. Choose a period opened by the M&E team.',
            ]);
        }

        $submission = $existingSubmission ?: $this->createSubmissionEnvelope(
            $memberState->id,
            $cycle->id,
            $user->id
        );

        $request->session()->put('member_state_reporting_submission_id', $submission->id);

        $message = $submission->wasRecentlyCreated
            ? 'Reporting workspace started. Only one report can be submitted for this country and reporting period.'
            : 'Your existing reporting workspace has been reopened. A duplicate report was not created.';

        return redirect()
            ->route('member-state.reporting.index', ['submission' => $submission->id])
            ->with('success', $message);
    }

    public function show(Request $request, string $section)
    {
        $reportingSection = collect(config('member_state_reporting.sections'))
            ->firstWhere('slug', $section);

        abort_unless($reportingSection, 404);

        $user = $request->user()->loadMissing('memberState');
        $memberState = $user->memberState;

        abort_unless($memberState, 403, 'A Member State must be assigned to your account.');

        $submission = $this->selectedSubmission($request, $memberState->id);

        if (! $submission) {
            return redirect()
                ->route('member-state.reporting.index')
                ->with('warning', 'Select a reporting frequency before opening a reporting section.');
        }

        return view('member-state.reporting-section', [
            'memberState' => $memberState,
            'reportingSection' => $reportingSection,
            'reportSubmission' => $submission,
        ]);
    }

    private function openCyclesByFrequency(): Collection
    {
        return MemberStateReportingCycle::query()
            ->with('reportingFrequency')
            ->where('status', MemberStateReportingCycle::STATUS_OPEN)
            ->where(function ($query): void {
                $query->whereNull('opens_at')->orWhere('opens_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('closes_at')->orWhere('closes_at', '>=', now());
            })
            ->whereHas('reportingFrequency', function ($query): void {
                $query->where('is_active', true)
                    ->whereIn('code', array_keys(self::FREQUENCY_OPTIONS));
            })
            ->orderByDesc('period_start')
            ->get()
            ->unique(fn (MemberStateReportingCycle $cycle): ?string => $cycle->reportingFrequency?->code)
            ->filter(fn (MemberStateReportingCycle $cycle): bool => (bool) $cycle->reportingFrequency?->code)
            ->keyBy(fn (MemberStateReportingCycle $cycle): string => (string) $cycle->reportingFrequency->code);
    }

    private function selectedSubmission(Request $request, string $memberStateId): ?MemberStateReportSubmission
    {
        $submissionId = (string) ($request->query('submission')
            ?: $request->session()->get('member_state_reporting_submission_id', ''));

        if ($submissionId === '') {
            return null;
        }

        $submission = MemberStateReportSubmission::query()
            ->with('reportingCycle.reportingFrequency')
            ->whereKey($submissionId)
            ->where('member_state_id', $memberStateId)
            ->first();

        if (! $submission) {
            $request->session()->forget('member_state_reporting_submission_id');
        }

        return $submission;
    }

    private function createSubmissionEnvelope(
        string $memberStateId,
        string $cycleId,
        string $userId
    ): MemberStateReportSubmission {
        try {
            return DB::transaction(fn (): MemberStateReportSubmission => MemberStateReportSubmission::firstOrCreate(
                [
                    'member_state_id' => $memberStateId,
                    'reporting_cycle_id' => $cycleId,
                ],
                [
                    'status' => MemberStateReportSubmission::STATUS_DRAFT,
                    'started_by' => $userId,
                    'started_at' => now(),
                ]
            ));
        } catch (QueryException $exception) {
            if (! in_array((string) $exception->getCode(), ['23000', '23505'], true)) {
                throw $exception;
            }

            return MemberStateReportSubmission::query()
                ->where('member_state_id', $memberStateId)
                ->where('reporting_cycle_id', $cycleId)
                ->firstOrFail();
        }
    }
}
