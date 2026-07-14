<?php

namespace App\Http\Controllers;

use App\Models\MemberStateReportingCycle;
use App\Models\ReportingFrequency;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MemberStateReportingCycleController extends Controller
{
    private const ALLOWED_FREQUENCY_CODES = [
        'QUARTERLY',
        'SEMI_ANNUAL',
        'ANNUAL',
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:me.configuration.view')->only('index');
        $this->middleware('permission:me.configuration.manage')->except('index');
    }

    public function index(): View
    {
        $cycles = MemberStateReportingCycle::query()
            ->with('reportingFrequency')
            ->withCount('submissions')
            ->orderByDesc('reporting_year')
            ->orderByDesc('period_number')
            ->paginate(20);

        return view('me.member-state-reporting-cycles.index', compact('cycles'));
    }

    public function create(): View
    {
        return view('me.member-state-reporting-cycles.create', [
            'frequencies' => $this->allowedFrequencies(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reporting_frequency_id' => [
                'required',
                'uuid',
                Rule::exists('me_reporting_frequencies', 'id')->where(
                    fn ($query) => $query
                        ->where('is_active', true)
                        ->whereIn('code', self::ALLOWED_FREQUENCY_CODES)
                ),
            ],
            'reporting_year' => ['required', 'integer', 'min:2000', 'max:'.(now()->year + 10)],
            'period_number' => ['required', 'integer', 'min:1', 'max:4'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => [
                'nullable',
                'date',
                ...($request->filled('opens_at') ? ['after:opens_at'] : []),
            ],
            'status' => ['required', Rule::in(array_keys(MemberStateReportingCycle::STATUSES))],
            'instructions' => ['nullable', 'string', 'max:10000'],
        ]);

        $frequency = $this->findAllowedFrequency($validated['reporting_frequency_id']);
        $identity = $this->derivePeriodIdentity(
            $frequency->code,
            (int) $validated['reporting_year'],
            (int) $validated['period_number']
        );

        $this->ensurePeriodIsUnique($frequency->id, $identity['period_key']);

        DB::transaction(function () use ($validated, $frequency, $identity): void {
            if ($validated['status'] === MemberStateReportingCycle::STATUS_OPEN) {
                $this->closeOtherOpenCycles($frequency->id);
            }

            MemberStateReportingCycle::create([
                'reporting_frequency_id' => $frequency->id,
                ...$identity,
                'opens_at' => $validated['opens_at'] ?? null,
                'closes_at' => $validated['closes_at'] ?? null,
                'status' => $validated['status'],
                'instructions' => $validated['instructions'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('budget.me.member-state-reporting-cycles.index')
            ->with('success', 'Member State reporting cycle created successfully.');
    }

    public function edit(MemberStateReportingCycle $memberStateReportingCycle): View
    {
        $memberStateReportingCycle->load('reportingFrequency')->loadCount('submissions');

        return view('me.member-state-reporting-cycles.edit', [
            'cycle' => $memberStateReportingCycle,
        ]);
    }

    public function update(
        Request $request,
        MemberStateReportingCycle $memberStateReportingCycle
    ): RedirectResponse {
        $validated = $request->validate([
            'opens_at' => ['nullable', 'date'],
            'closes_at' => [
                'nullable',
                'date',
                ...($request->filled('opens_at') ? ['after:opens_at'] : []),
            ],
            'status' => ['required', Rule::in(array_keys(MemberStateReportingCycle::STATUSES))],
            'instructions' => ['nullable', 'string', 'max:10000'],
        ]);

        DB::transaction(function () use ($validated, $memberStateReportingCycle): void {
            if ($validated['status'] === MemberStateReportingCycle::STATUS_OPEN) {
                $this->closeOtherOpenCycles(
                    $memberStateReportingCycle->reporting_frequency_id,
                    $memberStateReportingCycle->getKey()
                );
            }

            $memberStateReportingCycle->update([
                'opens_at' => $validated['opens_at'] ?? null,
                'closes_at' => $validated['closes_at'] ?? null,
                'status' => $validated['status'],
                'instructions' => $validated['instructions'] ?? null,
                'updated_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('budget.me.member-state-reporting-cycles.index')
            ->with('success', 'Member State reporting cycle updated successfully.');
    }

    public function destroy(MemberStateReportingCycle $memberStateReportingCycle): RedirectResponse
    {
        if ($memberStateReportingCycle->submissions()->exists()) {
            return back()->with(
                'error',
                'This reporting cycle cannot be deleted because Member States have already submitted data for it.'
            );
        }

        $memberStateReportingCycle->delete();

        return redirect()
            ->route('budget.me.member-state-reporting-cycles.index')
            ->with('success', 'Member State reporting cycle deleted successfully.');
    }

    private function allowedFrequencies()
    {
        return ReportingFrequency::query()
            ->active()
            ->whereIn('code', self::ALLOWED_FREQUENCY_CODES)
            ->orderByRaw("CASE code WHEN 'QUARTERLY' THEN 1 WHEN 'SEMI_ANNUAL' THEN 2 WHEN 'ANNUAL' THEN 3 ELSE 4 END")
            ->get(['id', 'name', 'code']);
    }

    private function findAllowedFrequency(string $frequencyId): ReportingFrequency
    {
        return ReportingFrequency::query()
            ->active()
            ->whereIn('code', self::ALLOWED_FREQUENCY_CODES)
            ->findOrFail($frequencyId);
    }

    /**
     * @return array{period_key: string, label: string, reporting_year: int, period_number: int, period_start: string, period_end: string}
     */
    private function derivePeriodIdentity(string $frequencyCode, int $year, int $periodNumber): array
    {
        $frequencyCode = strtoupper(trim($frequencyCode));
        $yearStart = CarbonImmutable::create($year, 1, 1)->startOfDay();

        [$periodKey, $label, $periodStart, $periodEnd] = match ($frequencyCode) {
            'QUARTERLY' => $this->quarterlyPeriod($yearStart, $year, $periodNumber),
            'SEMI_ANNUAL' => $this->semiAnnualPeriod($yearStart, $year, $periodNumber),
            'ANNUAL' => $this->annualPeriod($yearStart, $year, $periodNumber),
            default => throw ValidationException::withMessages([
                'reporting_frequency_id' => 'Only Quarterly, Semi-Annual and Annual reporting are supported.',
            ]),
        };

        return [
            'period_key' => $periodKey,
            'label' => $label,
            'reporting_year' => $year,
            'period_number' => $periodNumber,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
        ];
    }

    private function quarterlyPeriod(
        CarbonImmutable $yearStart,
        int $year,
        int $periodNumber
    ): array {
        if ($periodNumber < 1 || $periodNumber > 4) {
            throw ValidationException::withMessages([
                'period_number' => 'Quarterly reporting requires a period from Q1 to Q4.',
            ]);
        }

        $start = $yearStart->addMonths(($periodNumber - 1) * 3);

        return [
            $year.'-Q'.$periodNumber,
            'Quarter '.$periodNumber.', '.$year,
            $start,
            $start->addMonths(3)->subDay(),
        ];
    }

    private function semiAnnualPeriod(
        CarbonImmutable $yearStart,
        int $year,
        int $periodNumber
    ): array {
        if ($periodNumber < 1 || $periodNumber > 2) {
            throw ValidationException::withMessages([
                'period_number' => 'Semi-Annual reporting requires either H1 or H2.',
            ]);
        }

        $start = $yearStart->addMonths(($periodNumber - 1) * 6);

        return [
            $year.'-H'.$periodNumber,
            ($periodNumber === 1 ? 'First Half, ' : 'Second Half, ').$year,
            $start,
            $start->addMonths(6)->subDay(),
        ];
    }

    private function annualPeriod(
        CarbonImmutable $yearStart,
        int $year,
        int $periodNumber
    ): array {
        if ($periodNumber !== 1) {
            throw ValidationException::withMessages([
                'period_number' => 'Annual reporting has one period per year.',
            ]);
        }

        return [
            $year.'-ANNUAL',
            'Annual, '.$year,
            $yearStart,
            $yearStart->endOfYear(),
        ];
    }

    private function ensurePeriodIsUnique(string $frequencyId, string $periodKey): void
    {
        $exists = MemberStateReportingCycle::query()
            ->where('reporting_frequency_id', $frequencyId)
            ->where('period_key', $periodKey)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'period_number' => 'A reporting cycle already exists for the selected frequency and period.',
            ]);
        }
    }

    private function closeOtherOpenCycles(string $frequencyId, ?string $exceptId = null): void
    {
        MemberStateReportingCycle::query()
            ->where('reporting_frequency_id', $frequencyId)
            ->where('status', MemberStateReportingCycle::STATUS_OPEN)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->update([
                'status' => MemberStateReportingCycle::STATUS_CLOSED,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);
    }
}
