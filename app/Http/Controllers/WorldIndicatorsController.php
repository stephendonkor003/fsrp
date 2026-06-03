<?php

namespace App\Http\Controllers;

use App\Models\WorldBankCountry;
use App\Models\WorldBankIndicator;
use App\Models\WorldBankTopic;
use App\Models\WorldIndicatorSetting;
use App\Services\WorldIndicatorDataService;
use App\Services\WorldShapeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorldIndicatorsController extends Controller
{
    private const FSRP_FOCUS_SHAPE_COUNTRIES = [
        'Angola',
        'Botswana',
        'Burundi',
        'Comoros',
        'Democratic Republic of the Congo',
        'Djibouti',
        'Eritrea',
        'Ethiopia',
        'Kenya',
        'Lesotho',
        'Madagascar',
        'Malawi',
        'Mauritius',
        'Mozambique',
        'Namibia',
        'Rwanda',
        'Seychelles',
        'Somalia',
        'South Africa',
        'South Sudan',
        'Sudan',
        'Swaziland',
        'Tanzania',
        'Uganda',
        'Zambia',
        'Zimbabwe',
    ];

    protected WorldShapeService $worldShapeService;

    protected WorldIndicatorDataService $worldIndicatorDataService;

    public function __construct(
        WorldShapeService $worldShapeService,
        WorldIndicatorDataService $worldIndicatorDataService
    ) {
        $this->worldShapeService = $worldShapeService;
        $this->worldIndicatorDataService = $worldIndicatorDataService;
    }

    public function index()
    {
        $settings = $this->resolveSettings();

        if (!$settings->is_public_enabled) {
            abort(404);
        }

        $availableRegions = $this->worldShapeService->getAvailableRegions();
        $enabledRegions = collect($settings->enabled_regions ?? [])
            ->filter(function (string $region) use ($availableRegions): bool {
                return in_array($region, $availableRegions, true);
            })
            ->values()
            ->all();

        if (empty($enabledRegions)) {
            $enabledRegions = in_array('Africa', $availableRegions, true)
                ? ['Africa']
                : $availableRegions;
        }

        $defaultRegion = in_array((string) $settings->default_region, $enabledRegions, true)
            ? (string) $settings->default_region
            : ($enabledRegions[0] ?? null);

        $shapeFilesByRegion = $this->focusShapeFilesForFsrp(
            $this->worldShapeService->getShapeFilesByRegion($enabledRegions)
        );
        $countriesByRegion = $this->worldShapeService->getCountriesByRegion($shapeFilesByRegion);
        $regionLabels = $this->worldShapeService->getRegionLabels($enabledRegions);

        $enabledSources = collect([
            [
                'key' => 'imf',
                'label' => 'IMF Portal',
                'enabled' => $settings->imf_source_enabled,
                'endpoint' => $settings->imf_api_base_url,
            ],
            [
                'key' => 'world_bank',
                'label' => 'World Bank Portal',
                'enabled' => $settings->world_bank_source_enabled,
                'endpoint' => $settings->world_bank_api_base_url,
            ],
        ])->filter(function (array $source): bool {
            return (bool) $source['enabled'];
        })->values()->all();

        $summary = [
            'regions' => count($enabledRegions),
            'countries' => collect($countriesByRegion)->flatten()->unique()->count(),
            'shape_files' => collect($shapeFilesByRegion)->flatten()->count(),
        ];

        $worldBankSummary = [
            'topics' => WorldBankTopic::query()->count(),
            'indicators' => WorldBankIndicator::query()->count(),
            'countries' => WorldBankCountry::query()->where('is_aggregate', false)->count(),
        ];

        return view('world-indicators-performance', compact(
            'settings',
            'enabledRegions',
            'defaultRegion',
            'shapeFilesByRegion',
            'countriesByRegion',
            'regionLabels',
            'enabledSources',
            'summary',
            'worldBankSummary'
        ));
    }

    public function countryMetrics(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country' => ['required', 'string', 'max:140'],
        ]);

        $settings = $this->resolveSettings();

        if (!$settings->is_public_enabled) {
            abort(404);
        }

        $snapshot = $this->worldIndicatorDataService
            ->buildCountrySnapshot((string) $validated['country'], $settings);

        return response()->json($snapshot);
    }

    public function topics(): JsonResponse
    {
        $settings = $this->resolveSettings();
        if (!$settings->is_public_enabled) {
            abort(404);
        }

        return response()->json([
            'data' => $this->worldIndicatorDataService->getWorldBankTopics(),
        ]);
    }

    public function indicators(Request $request): JsonResponse
    {
        $settings = $this->resolveSettings();
        if (!$settings->is_public_enabled) {
            abort(404);
        }

        $validated = $request->validate([
            'topic_id' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:2000'],
        ]);

        return response()->json([
            'data' => $this->worldIndicatorDataService->getWorldBankIndicators(
                isset($validated['topic_id']) ? (int) $validated['topic_id'] : null,
                $validated['search'] ?? null,
                (int) ($validated['limit'] ?? 500)
            ),
        ]);
    }

    public function countries(Request $request): JsonResponse
    {
        $settings = $this->resolveSettings();
        if (!$settings->is_public_enabled) {
            abort(404);
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        return response()->json([
            'data' => $this->worldIndicatorDataService->getWorldBankCountries($validated['search'] ?? null),
        ]);
    }

    public function continents(): JsonResponse
    {
        $settings = $this->resolveSettings();
        if (!$settings->is_public_enabled) {
            abort(404);
        }

        return response()->json([
            'data' => $this->worldIndicatorDataService->getWorldBankContinents(),
        ]);
    }

    public function compare(Request $request): JsonResponse
    {
        $settings = $this->resolveSettings();
        if (!$settings->is_public_enabled) {
            abort(404);
        }

        $currentYear = (int) now()->year;
        $validated = $request->validate([
            'indicator_id' => ['required', 'string', 'max:80'],
            'compare_mode' => ['required', Rule::in(['country', 'continent'])],
            'countries' => ['required_if:compare_mode,country', 'array', 'min:1'],
            'countries.*' => ['string', 'size:2'],
            'continents' => ['required_if:compare_mode,continent', 'array', 'min:2'],
            'continents.*' => ['string', 'max:80'],
            'year_from' => ['required', 'integer', 'min:1960', 'max:' . $currentYear],
            'year_to' => ['required', 'integer', 'min:1960', 'max:' . $currentYear],
            'aggregation' => ['nullable', Rule::in(['avg', 'sum'])],
        ]);

        $payload = $this->worldIndicatorDataService->compareWorldBankData(
            (string) $validated['indicator_id'],
            (string) $validated['compare_mode'],
            $validated['countries'] ?? [],
            $validated['continents'] ?? [],
            (int) $validated['year_from'],
            (int) $validated['year_to'],
            (string) ($validated['aggregation'] ?? 'avg')
        );

        return response()->json($payload);
    }

    private function resolveSettings(): WorldIndicatorSetting
    {
        return WorldIndicatorSetting::query()->firstOrCreate([], [
            'page_title' => 'Food Security Indicator Analytics',
            'page_intro' => 'Compare FSRP food-system resilience, macroeconomic, agriculture, and member-state indicators for Eastern and Southern Africa using IMF and World Bank sources.',
            'is_public_enabled' => true,
            'imf_source_enabled' => true,
            'world_bank_source_enabled' => true,
            'enabled_regions' => ['Africa'],
            'default_region' => 'Africa',
        ]);
    }

    /**
     * @param array<string, array<int, string>> $shapeFilesByRegion
     * @return array<string, array<int, string>>
     */
    private function focusShapeFilesForFsrp(array $shapeFilesByRegion): array
    {
        $focusCountries = array_flip(self::FSRP_FOCUS_SHAPE_COUNTRIES);

        foreach ($shapeFilesByRegion as $region => $shapeFiles) {
            if ($region !== 'Africa') {
                continue;
            }

            $shapeFilesByRegion[$region] = array_values(array_filter($shapeFiles, function (string $shapeFile) use ($focusCountries): bool {
                $path = parse_url($shapeFile, PHP_URL_PATH) ?: $shapeFile;
                $countryName = rawurldecode((string) pathinfo($path, PATHINFO_FILENAME));

                return isset($focusCountries[$countryName]);
            }));
        }

        return $shapeFilesByRegion;
    }
}
