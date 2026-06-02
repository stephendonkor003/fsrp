<?php

namespace App\Http\Controllers;

use App\Models\AuMemberState;
use App\Models\Commodity;
use App\Models\MemberStateCommodityTrend;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class FoodSecurityAnalyticsController extends Controller
{
    private const COMMODITY_METRICS = [
        'production_volume_total' => 'metric_production_volume',
        'stock_volume_total' => 'metric_stock_volume',
        'import_volume_total' => 'metric_import_volume',
        'export_volume_total' => 'metric_export_volume',
        'export_value_total' => 'metric_export_value',
        'avg_market_price' => 'metric_avg_market_price',
        'avg_availability_score' => 'metric_avg_availability_score',
        'avg_growth_rate' => 'metric_avg_growth_rate',
        'data_points' => 'metric_data_points',
    ];

    public function commodities(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $metricOptions = $this->commodityMetricOptions();
        $metric = $this->metric($request, $metricOptions, 'production_volume_total');
        $selectedStateIds = $this->selectedMemberStateIds($request);
        $commodityId = (string) $request->input('commodity_id', '');

        $recordsQuery = MemberStateCommodityTrend::query()
            ->approved()
            ->with([
                'memberState:id,name,code,code_alpha2',
                'commodity:id,name,category,unit_of_measure',
            ])
            ->whereBetween('recorded_on', [$from, $to]);

        if ($commodityId !== '') {
            $recordsQuery->where('commodity_id', $commodityId);
        }

        $records = $recordsQuery->get();

        $stateRows = $records
            ->groupBy('member_state_id')
            ->map(fn (Collection $rows) => $this->commodityStateRow($rows))
            ->filter()
            ->sortByDesc(fn (array $row) => (float) ($row['metrics'][$metric] ?? 0))
            ->values();

        $comparisonRows = $this->comparisonRows($stateRows, $selectedStateIds, $metric);

        return view('analytics.member-state-map', [
            'page' => [
                'title' => __('public_pages.food_commodities_title'),
                'eyebrow' => __('public_pages.food_commodities_eyebrow'),
                'intro' => __('public_pages.food_commodities_intro'),
                'route' => route('food-security.commodities'),
                'active' => 'commodities',
                'map_title' => __('public_pages.food_commodities_title'),
                'map_hint' => __('public_pages.food_commodities_map_hint'),
                'empty_text' => __('public_pages.food_commodities_empty'),
            ],
            'metricOptions' => $metricOptions,
            'filters' => array_merge($this->filters($from, $to, $metric, $selectedStateIds), [
                'commodity_id' => $commodityId,
            ]),
            'memberStates' => $this->memberStates(),
            'commodityOptions' => Commodity::query()->orderBy('name')->get(['id', 'name', 'category']),
            'stateRows' => $stateRows,
            'comparisonRows' => $comparisonRows,
            'mapData' => $this->mapData($stateRows),
            'shapeFiles' => $this->getAfricaShapeFiles(),
            'summaryCards' => [
                ['label' => __('public_pages.summary_approved_commodity_records'), 'value' => number_format($records->count())],
                ['label' => __('public_pages.summary_member_states_with_data'), 'value' => number_format($stateRows->count())],
                ['label' => __('public_pages.summary_commodities_tracked'), 'value' => number_format($records->pluck('commodity_id')->unique()->count())],
                ['label' => __('public_pages.summary_avg_availability'), 'value' => number_format((float) $stateRows->avg('metrics.avg_availability_score'), 1) . '%'],
            ],
        ]);
    }

    private function commodityMetricOptions(): array
    {
        return collect(self::COMMODITY_METRICS)
            ->mapWithKeys(fn (string $translationKey, string $metric): array => [
                $metric => __('public_pages.' . $translationKey),
            ])
            ->all();
    }

    private function commodityStateRow(Collection $rows): ?array
    {
        $memberState = $rows->first()?->memberState;
        if (!$memberState) {
            return null;
        }

        $latest = $rows->sortByDesc('recorded_on')->first();
        $commodityNames = $rows->pluck('commodity.name')->filter()->unique()->sort()->values();

        return $this->stateRow($memberState, [
            'production_volume_total' => round((float) $rows->sum('production_volume'), 3),
            'stock_volume_total' => round((float) $rows->sum('stock_volume'), 3),
            'import_volume_total' => round((float) $rows->sum('import_volume'), 3),
            'export_volume_total' => round((float) $rows->sum('export_volume'), 3),
            'export_value_total' => round((float) $rows->sum('export_value_usd'), 2),
            'avg_market_price' => round($this->average($rows, 'market_price'), 2),
            'avg_availability_score' => round($this->average($rows, 'availability_score'), 2),
            'avg_growth_rate' => round($this->average($rows, 'growth_rate_pct'), 3),
            'data_points' => $rows->count(),
        ], [
            'latest_recorded_on' => optional($latest?->recorded_on)->format('d M Y'),
            'latest_summary' => (string) ($latest?->trend_summary ?: $latest?->impact_notes ?: ''),
            'commodities' => $commodityNames->take(6)->implode(', '),
            'commodity_count' => $commodityNames->count(),
        ]);
    }

    private function stateRow(AuMemberState $memberState, array $metrics, array $meta = []): array
    {
        return [
            'member_state_id' => $memberState->id,
            'name' => $memberState->name,
            'code' => $memberState->code,
            'code_alpha2' => $memberState->code_alpha2,
            'metrics' => $metrics,
            'meta' => $meta,
        ];
    }

    private function comparisonRows(Collection $stateRows, Collection $selectedStateIds, string $metric): Collection
    {
        $rows = $selectedStateIds->isNotEmpty()
            ? $stateRows->filter(fn (array $row) => $selectedStateIds->contains($row['member_state_id']))
            : $stateRows;

        return $rows
            ->sortByDesc(fn (array $row) => (float) ($row['metrics'][$metric] ?? 0))
            ->take(12)
            ->values();
    }

    private function mapData(Collection $stateRows): array
    {
        return $stateRows
            ->mapWithKeys(function (array $row) {
                $code = strtoupper((string) ($row['code_alpha2'] ?: $row['code'] ?: ''));
                if ($code === '') {
                    return [];
                }

                return [$code => $row];
            })
            ->all();
    }

    private function average(Collection $rows, string $column): float
    {
        $values = $rows
            ->pluck($column)
            ->filter(fn ($value) => $value !== null && $value !== '');

        return (float) ($values->avg() ?? 0);
    }

    private function dateRange(Request $request): array
    {
        $from = $this->parseDate((string) $request->input('from'), now()->subYears(3)->startOfYear());
        $to = $this->parseDate((string) $request->input('to'), now()->endOfYear());

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from->toDateString(), $to->toDateString()];
    }

    private function parseDate(string $value, Carbon $fallback): Carbon
    {
        try {
            return trim($value) !== '' ? Carbon::parse($value) : $fallback;
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function metric(Request $request, array $options, string $fallback): string
    {
        $metric = (string) $request->input('metric', $fallback);

        return array_key_exists($metric, $options) ? $metric : $fallback;
    }

    private function selectedMemberStateIds(Request $request): Collection
    {
        $validIds = $this->memberStates()->pluck('id');

        return collect((array) $request->input('member_state_ids', []))
            ->filter()
            ->intersect($validIds)
            ->values();
    }

    private function filters(string $from, string $to, string $metric, Collection $selectedStateIds): array
    {
        return [
            'from' => $from,
            'to' => $to,
            'metric' => $metric,
            'member_state_ids' => $selectedStateIds->all(),
        ];
    }

    private function memberStates(): Collection
    {
        return AuMemberState::query()
            ->active()
            ->ordered()
            ->get(['id', 'name', 'code', 'code_alpha2']);
    }

    private function getAfricaShapeFiles(): array
    {
        $directory = public_path('assets/Africa');
        if (!File::exists($directory)) {
            return [];
        }

        $baseUrl = app()->bound('request') ? rtrim(request()->getBaseUrl(), '/') : '';
        $assetPathPrefix = ($baseUrl !== '' ? $baseUrl : '') . '/assets/Africa/';

        return collect(File::files($directory))
            ->filter(function ($file): bool {
                return in_array(strtolower((string) $file->getExtension()), ['geojson', 'json', 'shp'], true);
            })
            ->sortBy(fn ($file): string => (string) $file->getFilename())
            ->map(fn ($file): string => $assetPathPrefix . rawurlencode((string) $file->getFilename()))
            ->values()
            ->all();
    }
}
