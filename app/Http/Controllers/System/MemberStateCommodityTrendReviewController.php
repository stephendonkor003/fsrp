<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\AuMemberState;
use App\Models\Commodity;
use App\Models\MemberStateCommodityTrend;
use App\Models\SystemAuditLog;
use App\Support\IpGeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MemberStateCommodityTrendReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:commodity_data.review')->only(['index']);
        $this->middleware('permission:commodity_data.approve')->only(['updateStatus']);
    }

    public function index(Request $request)
    {
        $baseQuery = MemberStateCommodityTrend::query();

        $query = MemberStateCommodityTrend::query()
            ->with([
                'memberState:id,name,code,code_alpha2',
                'commodity:id,name,category,unit_of_measure',
                'creator:id,name,email',
                'reviewer:id,name,email',
            ]);

        if ($request->filled('member_state_id')) {
            $query->where('member_state_id', $request->input('member_state_id'));
        }

        if ($request->filled('commodity_id')) {
            $query->where('commodity_id', $request->input('commodity_id'));
        }

        if ($request->filled('review_status')) {
            $query->where('review_status', $request->input('review_status'));
        }

        if ($request->filled('from')) {
            $query->whereDate('recorded_on', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('recorded_on', '<=', $request->input('to'));
        }

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($inner) use ($term) {
                $inner->where('trend_summary', 'like', '%' . $term . '%')
                    ->orWhere('impact_notes', 'like', '%' . $term . '%')
                    ->orWhereHas('memberState', function ($memberStateQuery) use ($term) {
                        $memberStateQuery->where('name', 'like', '%' . $term . '%')
                            ->orWhere('code', 'like', '%' . $term . '%')
                            ->orWhere('code_alpha2', 'like', '%' . $term . '%');
                    })
                    ->orWhereHas('commodity', function ($commodityQuery) use ($term) {
                        $commodityQuery->where('name', 'like', '%' . $term . '%')
                            ->orWhere('category', 'like', '%' . $term . '%');
                    });
            });
        }

        $entries = $query
            ->orderByRaw("
                CASE review_status
                    WHEN 'pending' THEN 1
                    WHEN 'revision_required' THEN 2
                    WHEN 'rejected' THEN 3
                    WHEN 'approved' THEN 4
                    ELSE 5
                END
            ")
            ->orderByDesc('recorded_on')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('review_status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('review_status', 'approved')->count(),
            'revision_required' => (clone $baseQuery)->where('review_status', 'revision_required')->count(),
            'rejected' => (clone $baseQuery)->where('review_status', 'rejected')->count(),
            'approved_avg_availability' => (float) ((clone $baseQuery)->where('review_status', 'approved')->avg('availability_score') ?? 0),
        ];

        $memberStates = AuMemberState::query()->active()->ordered()->get(['id', 'name']);
        $commodities = Commodity::query()->orderBy('name')->get(['id', 'name', 'category']);

        return view('system.commodity-trend-reviews.index', [
            'entries' => $entries,
            'memberStates' => $memberStates,
            'commodities' => $commodities,
            'stats' => $stats,
            'filters' => [
                'q' => (string) $request->input('q', ''),
                'member_state_id' => (string) $request->input('member_state_id', ''),
                'commodity_id' => (string) $request->input('commodity_id', ''),
                'review_status' => (string) $request->input('review_status', ''),
                'from' => (string) $request->input('from', ''),
                'to' => (string) $request->input('to', ''),
            ],
        ]);
    }

    public function updateStatus(Request $request, MemberStateCommodityTrend $entry)
    {
        $validated = $request->validate([
            'review_status' => ['required', 'in:pending,approved,revision_required,rejected'],
            'review_notes' => ['nullable', 'string', 'max:12000'],
        ]);

        $isPending = $validated['review_status'] === 'pending';

        $entry->update([
            'review_status' => $validated['review_status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by' => $isPending ? null : $request->user()->id,
            'reviewed_at' => $isPending ? null : now(),
            'updated_by' => $request->user()->id,
        ]);

        $this->logReviewAudit(
            $request,
            'commodity_trend_review_status_updated',
            'Back-office reviewer updated commodity trend review status.',
            [
                'entry_id' => $entry->id,
                'member_state_id' => $entry->member_state_id,
                'commodity_id' => $entry->commodity_id,
                'review_status' => $validated['review_status'],
            ]
        );

        return back()->with('success', 'Commodity trend review status updated successfully.');
    }

    private function logReviewAudit(
        Request $request,
        string $action,
        string $actionMessage,
        array $payload = []
    ): void {
        try {
            SystemAuditLog::create([
                'user_id' => optional($request->user())->id,
                'module' => 'commodity_data',
                'action' => $action,
                'action_message' => $actionMessage,
                'description' => $actionMessage,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route_name' => optional($request->route())->getName(),
                'ip_address' => $request->ip(),
                'country' => IpGeo::countryForIp($request->ip()),
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                'status_code' => 200,
                'payload' => $payload,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Failed to persist commodity trend review audit log entry.', [
                'action' => $action,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
