<?php

namespace App\Services;

use App\Models\AuMemberState;
use App\Models\WorldBankCountry;
use App\Models\WorldBankIndicator;
use App\Models\WorldBankIndicatorObservation;
use App\Models\WorldBankTopic;
use App\Models\WorldIndicatorSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class WorldIndicatorDataService
{
    private const DEFAULT_WORLD_BANK_SNAPSHOT_INDICATORS = [
        'SP.POP.TOTL',
        'NY.GDP.MKTP.CD',
        'SL.UEM.TOTL.ZS',
    ];

    private const DEFAULT_WORLD_BANK_TOPICS = [
        ['id' => 9001, 'name' => 'FSRP Core Indicators'],
        ['id' => 1, 'name' => 'Agriculture and Rural Development'],
        ['id' => 3, 'name' => 'Economy and Growth'],
        ['id' => 6, 'name' => 'Environment'],
        ['id' => 8, 'name' => 'Health'],
    ];

    private const DEFAULT_WORLD_BANK_INDICATORS = [
        [
            'id' => 'SP.POP.TOTL',
            'name' => 'Population, total',
            'unit' => 'Number',
            'topics' => [9001],
        ],
        [
            'id' => 'NY.GDP.MKTP.CD',
            'name' => 'GDP, current US$',
            'unit' => 'Current US$',
            'topics' => [9001, 3],
        ],
        [
            'id' => 'NY.GDP.PCAP.CD',
            'name' => 'GDP per capita, current US$',
            'unit' => 'Current US$',
            'topics' => [9001, 3],
        ],
        [
            'id' => 'SL.UEM.TOTL.ZS',
            'name' => 'Unemployment, total (% of total labor force)',
            'unit' => 'Percent',
            'topics' => [9001, 3],
        ],
        [
            'id' => 'AG.PRD.FOOD.XD',
            'name' => 'Food production index',
            'unit' => 'Index',
            'topics' => [9001, 1],
        ],
        [
            'id' => 'AG.LND.AGRI.ZS',
            'name' => 'Agricultural land (% of land area)',
            'unit' => 'Percent',
            'topics' => [9001, 1],
        ],
        [
            'id' => 'NV.AGR.TOTL.ZS',
            'name' => 'Agriculture, forestry, and fishing, value added (% of GDP)',
            'unit' => 'Percent',
            'topics' => [9001, 1, 3],
        ],
        [
            'id' => 'SN.ITK.DEFC.ZS',
            'name' => 'Prevalence of undernourishment (% of population)',
            'unit' => 'Percent',
            'topics' => [9001, 8],
        ],
        [
            'id' => 'EG.ELC.ACCS.ZS',
            'name' => 'Access to electricity (% of population)',
            'unit' => 'Percent',
            'topics' => [9001, 6],
        ],
        [
            'id' => 'EN.ATM.CO2E.PC',
            'name' => 'CO2 emissions (metric tons per capita)',
            'unit' => 'Metric tons per capita',
            'topics' => [9001, 6],
        ],
    ];

    private const DEFAULT_FSRP_COUNTRIES = [
        ['iso2' => 'AO', 'iso3' => 'AGO', 'name' => 'Angola'],
        ['iso2' => 'BW', 'iso3' => 'BWA', 'name' => 'Botswana'],
        ['iso2' => 'BI', 'iso3' => 'BDI', 'name' => 'Burundi'],
        ['iso2' => 'KM', 'iso3' => 'COM', 'name' => 'Comoros'],
        ['iso2' => 'CD', 'iso3' => 'COD', 'name' => 'Democratic Republic of the Congo'],
        ['iso2' => 'DJ', 'iso3' => 'DJI', 'name' => 'Djibouti'],
        ['iso2' => 'ER', 'iso3' => 'ERI', 'name' => 'Eritrea'],
        ['iso2' => 'SZ', 'iso3' => 'SWZ', 'name' => 'Eswatini'],
        ['iso2' => 'ET', 'iso3' => 'ETH', 'name' => 'Ethiopia'],
        ['iso2' => 'KE', 'iso3' => 'KEN', 'name' => 'Kenya'],
        ['iso2' => 'LS', 'iso3' => 'LSO', 'name' => 'Lesotho'],
        ['iso2' => 'MG', 'iso3' => 'MDG', 'name' => 'Madagascar'],
        ['iso2' => 'MW', 'iso3' => 'MWI', 'name' => 'Malawi'],
        ['iso2' => 'MU', 'iso3' => 'MUS', 'name' => 'Mauritius'],
        ['iso2' => 'MZ', 'iso3' => 'MOZ', 'name' => 'Mozambique'],
        ['iso2' => 'NA', 'iso3' => 'NAM', 'name' => 'Namibia'],
        ['iso2' => 'RW', 'iso3' => 'RWA', 'name' => 'Rwanda'],
        ['iso2' => 'SC', 'iso3' => 'SYC', 'name' => 'Seychelles'],
        ['iso2' => 'SO', 'iso3' => 'SOM', 'name' => 'Somalia'],
        ['iso2' => 'ZA', 'iso3' => 'ZAF', 'name' => 'South Africa'],
        ['iso2' => 'SS', 'iso3' => 'SSD', 'name' => 'South Sudan'],
        ['iso2' => 'SD', 'iso3' => 'SDN', 'name' => 'Sudan'],
        ['iso2' => 'TZ', 'iso3' => 'TZA', 'name' => 'Tanzania'],
        ['iso2' => 'UG', 'iso3' => 'UGA', 'name' => 'Uganda'],
        ['iso2' => 'ZM', 'iso3' => 'ZMB', 'name' => 'Zambia'],
        ['iso2' => 'ZW', 'iso3' => 'ZWE', 'name' => 'Zimbabwe'],
    ];

    public function __construct(protected WorldBankObservationSyncService $worldBankObservationSyncService)
    {
    }

    public function buildCountrySnapshot(string $country, WorldIndicatorSetting $settings): array
    {
        $cleanCountry = trim($country);
        $seed = (int) sprintf('%u', crc32(Str::lower($cleanCountry)));

        $sources = [];
        $isPlaceholder = false;

        if ($settings->imf_source_enabled) {
            $sources[] = [
                'key' => 'imf',
                'label' => 'IMF Portal',
                'note' => $settings->imf_api_base_url
                    ? 'Endpoint configured in back office. Live mapping will be applied once indicator API keys are finalized.'
                    : 'Endpoint is not configured yet. Placeholder preview values are shown.',
                'metrics' => $this->buildImfMetrics($seed),
            ];
            $isPlaceholder = true;
        }

        if ($settings->world_bank_source_enabled) {
            $worldBankSource = $this->buildWorldBankSource($cleanCountry, $settings, $seed);
            $sources[] = $worldBankSource['source'];
            $isPlaceholder = $isPlaceholder || $worldBankSource['placeholder'];
        }

        return [
            'country' => $cleanCountry,
            'placeholder' => $isPlaceholder,
            'generated_at' => now()->toIso8601String(),
            'sources' => $sources,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getWorldBankTopics(): array
    {
        if (!WorldBankTopic::query()->exists() || !WorldBankIndicator::query()->exists()) {
            return $this->fallbackWorldBankTopics();
        }

        return WorldBankTopic::query()
            ->withCount('indicators')
            ->get(['id', 'wb_topic_id', 'name'])
            ->sortBy(fn (WorldBankTopic $topic): int => $topic->wb_topic_id === 9001 ? 0 : (int) $topic->wb_topic_id)
            ->map(function (WorldBankTopic $topic): array {
                return [
                    'id' => $topic->wb_topic_id,
                    'name' => $topic->name,
                    'indicator_count' => (int) $topic->indicators_count,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getWorldBankIndicators(?int $topicId = null, ?string $search = null, int $limit = 500): array
    {
        if (!WorldBankIndicator::query()->exists()) {
            return $this->fallbackWorldBankIndicators($topicId, $search, $limit);
        }

        $query = WorldBankIndicator::query()
            ->with('topics:id,wb_topic_id,name')
            ->orderBy('name')
            ->limit(max(1, min($limit, 2000)));

        if ($topicId !== null && $topicId > 0) {
            $query->whereHas('topics', function ($topicQuery) use ($topicId): void {
                $topicQuery->where('wb_topic_id', $topicId);
            });
        }

        $searchTerm = trim((string) $search);
        if ($searchTerm !== '') {
            $query->where(function ($inner) use ($searchTerm): void {
                $inner->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('wb_indicator_id', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query
            ->get(['id', 'wb_indicator_id', 'name', 'unit'])
            ->map(function (WorldBankIndicator $indicator): array {
                return [
                    'id' => $indicator->wb_indicator_id,
                    'name' => $indicator->name,
                    'unit' => $indicator->unit,
                    'topics' => $indicator->topics->map(function (WorldBankTopic $topic): array {
                        return [
                            'id' => $topic->wb_topic_id,
                            'name' => $topic->name,
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getWorldBankCountries(?string $search = null): array
    {
        if (!WorldBankCountry::query()->where('is_aggregate', false)->exists()) {
            return $this->fallbackWorldBankCountries($search);
        }

        $query = WorldBankCountry::query()
            ->where('is_aggregate', false)
            ->whereNotNull('iso2_code')
            ->orderBy('name');

        $searchTerm = trim((string) $search);
        if ($searchTerm !== '') {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        return $query
            ->limit(400)
            ->get(['wb_country_id', 'iso2_code', 'name', 'continent', 'region'])
            ->map(function (WorldBankCountry $country): array {
                return [
                    'iso2' => strtoupper((string) $country->iso2_code),
                    'iso3' => strtoupper((string) $country->wb_country_id),
                    'name' => $country->name,
                    'continent' => $country->continent,
                    'region' => $country->region,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function getWorldBankContinents(): array
    {
        $continents = WorldBankCountry::query()
            ->where('is_aggregate', false)
            ->whereNotNull('continent')
            ->where('continent', '!=', '')
            ->distinct()
            ->orderBy('continent')
            ->pluck('continent')
            ->values()
            ->all();

        return $continents ?: ['Africa'];
    }

    /**
     * @param  array<int, string>  $countries
     * @param  array<int, string>  $continents
     * @return array<string, mixed>
     */
    public function compareWorldBankData(
        string $wbIndicatorId,
        string $compareMode,
        array $countries,
        array $continents,
        int $yearFrom,
        int $yearTo,
        string $aggregation = 'avg'
    ): array {
        $mode = in_array($compareMode, ['country', 'continent'], true) ? $compareMode : 'country';
        [$yearFrom, $yearTo] = $this->normalizeYearRange($yearFrom, $yearTo);
        $this->ensureDefaultWorldBankCatalog();

        $indicator = WorldBankIndicator::query()
            ->with('topics:id,wb_topic_id,name')
            ->where('wb_indicator_id', $wbIndicatorId)
            ->first();

        if (!$indicator) {
            throw new RuntimeException("Unknown World Bank indicator: {$wbIndicatorId}");
        }

        $this->worldBankObservationSyncService->ensureIndicatorRangeAvailable($indicator->wb_indicator_id, $yearFrom, $yearTo);

        return $mode === 'continent'
            ? $this->buildContinentComparison($indicator, $continents, $yearFrom, $yearTo, $aggregation)
            : $this->buildCountryComparison($indicator, $countries, $yearFrom, $yearTo);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fallbackWorldBankTopics(): array
    {
        $indicatorCounts = [];

        foreach (self::DEFAULT_WORLD_BANK_INDICATORS as $indicator) {
            foreach ($indicator['topics'] as $topicId) {
                $indicatorCounts[$topicId] = ($indicatorCounts[$topicId] ?? 0) + 1;
            }
        }

        return collect(self::DEFAULT_WORLD_BANK_TOPICS)
            ->map(function (array $topic) use ($indicatorCounts): array {
                return [
                    'id' => $topic['id'],
                    'name' => $topic['name'],
                    'indicator_count' => $indicatorCounts[$topic['id']] ?? 0,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fallbackWorldBankIndicators(?int $topicId = null, ?string $search = null, int $limit = 500): array
    {
        $topicNames = collect(self::DEFAULT_WORLD_BANK_TOPICS)
            ->mapWithKeys(fn (array $topic): array => [$topic['id'] => $topic['name']])
            ->all();
        $searchTerm = Str::lower(trim((string) $search));

        return collect(self::DEFAULT_WORLD_BANK_INDICATORS)
            ->filter(function (array $indicator) use ($topicId): bool {
                return $topicId === null || $topicId <= 0 || in_array($topicId, $indicator['topics'], true);
            })
            ->filter(function (array $indicator) use ($searchTerm): bool {
                if ($searchTerm === '') {
                    return true;
                }

                return str_contains(Str::lower($indicator['id']), $searchTerm)
                    || str_contains(Str::lower($indicator['name']), $searchTerm);
            })
            ->take(max(1, min($limit, 2000)))
            ->map(function (array $indicator) use ($topicNames): array {
                return [
                    'id' => $indicator['id'],
                    'name' => $indicator['name'],
                    'unit' => $indicator['unit'],
                    'topics' => collect($indicator['topics'])
                        ->map(fn (int $topicId): array => [
                            'id' => $topicId,
                            'name' => $topicNames[$topicId] ?? 'FSRP Indicators',
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fallbackWorldBankCountries(?string $search = null): array
    {
        $searchTerm = Str::lower(trim((string) $search));

        return collect(self::DEFAULT_FSRP_COUNTRIES)
            ->filter(function (array $country) use ($searchTerm): bool {
                return $searchTerm === '' || str_contains(Str::lower($country['name']), $searchTerm);
            })
            ->map(fn (array $country): array => [
                'iso2' => $country['iso2'],
                'iso3' => $country['iso3'],
                'name' => $country['name'],
                'continent' => 'Africa',
                'region' => 'Eastern and Southern Africa',
            ])
            ->values()
            ->all();
    }

    private function ensureDefaultWorldBankCatalog(): void
    {
        if (WorldBankIndicator::query()->whereIn('wb_indicator_id', array_column(self::DEFAULT_WORLD_BANK_INDICATORS, 'id'))->count() === count(self::DEFAULT_WORLD_BANK_INDICATORS)
            && WorldBankCountry::query()->whereIn('iso2_code', array_column(self::DEFAULT_FSRP_COUNTRIES, 'iso2'))->count() === count(self::DEFAULT_FSRP_COUNTRIES)) {
            return;
        }

        DB::transaction(function (): void {
            $topicModels = [];

            foreach (self::DEFAULT_WORLD_BANK_TOPICS as $topic) {
                $topicModels[$topic['id']] = WorldBankTopic::query()->updateOrCreate(
                    ['wb_topic_id' => $topic['id']],
                    [
                        'name' => $topic['name'],
                        'source_note' => 'Default FSRP Eastern and Southern Africa indicator group.',
                        'metadata' => ['fsrp_default' => true],
                    ]
                );
            }

            foreach (self::DEFAULT_WORLD_BANK_INDICATORS as $indicatorRow) {
                $indicator = WorldBankIndicator::query()->updateOrCreate(
                    ['wb_indicator_id' => $indicatorRow['id']],
                    [
                        'name' => $indicatorRow['name'],
                        'unit' => $indicatorRow['unit'],
                        'source_note' => 'Default FSRP indicator from the World Bank Indicators API.',
                        'source_name' => 'World Development Indicators',
                        'metadata' => ['fsrp_default' => true],
                    ]
                );

                foreach ($indicatorRow['topics'] as $topicId) {
                    if (!isset($topicModels[$topicId])) {
                        continue;
                    }

                    DB::table('world_bank_indicator_topic')->insertOrIgnore([
                        'world_bank_indicator_id' => $indicator->id,
                        'world_bank_topic_id' => $topicModels[$topicId]->id,
                    ]);
                }
            }

            foreach (self::DEFAULT_FSRP_COUNTRIES as $country) {
                WorldBankCountry::query()->updateOrCreate(
                    ['wb_country_id' => $country['iso3']],
                    [
                        'iso2_code' => $country['iso2'],
                        'name' => $country['name'],
                        'region' => 'Eastern and Southern Africa',
                        'admin_region' => 'Eastern and Southern Africa',
                        'continent' => 'Africa',
                        'is_aggregate' => false,
                    ]
                );
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function buildWorldBankSource(string $country, WorldIndicatorSetting $settings, int $seed): array
    {
        $countryIso2 = $this->resolveCountryIso2ByName($country);
        $yearTo = (int) now()->year;
        $yearFrom = max(1960, $yearTo - 30);

        if ($countryIso2 === null) {
            return [
                'placeholder' => true,
                'source' => [
                    'key' => 'world_bank',
                    'label' => 'World Bank Portal',
                    'note' => 'Country mapping is not available yet. Placeholder preview values are shown.',
                    'metrics' => $this->buildWorldBankMetrics($seed),
                ],
            ];
        }

        $snapshotIndicators = WorldBankIndicator::query()
            ->whereIn('wb_indicator_id', self::DEFAULT_WORLD_BANK_SNAPSHOT_INDICATORS)
            ->get(['id', 'wb_indicator_id', 'name', 'unit']);

        if ($snapshotIndicators->isEmpty()) {
            return [
                'placeholder' => true,
                'source' => [
                    'key' => 'world_bank',
                    'label' => 'World Bank Portal',
                    'note' => 'Local World Bank catalog has not been synced yet. Placeholder preview values are shown.',
                    'metrics' => $this->buildWorldBankMetrics($seed),
                ],
            ];
        }

        $metrics = [];

        foreach ($snapshotIndicators as $indicator) {
            $latestObservation = WorldBankIndicatorObservation::query()
                ->where('world_bank_indicator_id', $indicator->id)
                ->where('country_iso2', $countryIso2)
                ->whereNotNull('value')
                ->orderByDesc('year')
                ->first();

            if (!$latestObservation) {
                $this->worldBankObservationSyncService->syncIndicator($indicator->wb_indicator_id, $yearFrom, $yearTo);
                $latestObservation = WorldBankIndicatorObservation::query()
                    ->where('world_bank_indicator_id', $indicator->id)
                    ->where('country_iso2', $countryIso2)
                    ->whereNotNull('value')
                    ->orderByDesc('year')
                    ->first();
            }

            if ($latestObservation) {
                $formattedValue = $this->formatWorldBankValue((float) $latestObservation->value, $indicator->unit);
                $metrics[] = [
                    'label' => $indicator->name,
                    'value' => "{$formattedValue} ({$latestObservation->year})",
                ];
            }
        }

        if (empty($metrics)) {
            return [
                'placeholder' => true,
                'source' => [
                    'key' => 'world_bank',
                    'label' => 'World Bank Portal',
                    'note' => 'No live values are cached yet for this country. Placeholder preview values are shown.',
                    'metrics' => $this->buildWorldBankMetrics($seed),
                ],
            ];
        }

        return [
            'placeholder' => false,
            'source' => [
                'key' => 'world_bank',
                'label' => 'World Bank Portal',
                'note' => $settings->world_bank_api_base_url
                    ? 'Live values loaded from the World Bank API and cached in the platform.'
                    : 'Live values loaded from cached World Bank API sync data.',
                'metrics' => $metrics,
            ],
        ];
    }

    /**
     * @param  array<int, string>  $countries
     * @return array<string, mixed>
     */
    private function buildCountryComparison(WorldBankIndicator $indicator, array $countries, int $yearFrom, int $yearTo): array
    {
        $selectedCountries = collect($countries)
            ->map(function (string $iso2): string {
                return strtoupper(trim($iso2));
            })
            ->filter(function (string $iso2): bool {
                return strlen($iso2) === 2;
            })
            ->unique()
            ->values()
            ->all();

        if (empty($selectedCountries)) {
            $selectedCountries = WorldBankCountry::query()
                ->where('is_aggregate', false)
                ->whereNotNull('iso2_code')
                ->orderBy('name')
                ->limit(2)
                ->pluck('iso2_code')
                ->map(function (string $iso2): string {
                    return strtoupper($iso2);
                })
                ->all();
        }

        $rows = DB::table('world_bank_indicator_observations as o')
            ->join('world_bank_countries as c', 'c.iso2_code', '=', 'o.country_iso2')
            ->where('o.world_bank_indicator_id', $indicator->id)
            ->whereBetween('o.year', [$yearFrom, $yearTo])
            ->whereIn('o.country_iso2', $selectedCountries)
            ->where('c.is_aggregate', false)
            ->whereNotNull('o.value')
            ->orderBy('o.year')
            ->get([
                'o.country_iso2',
                'c.name as country_name',
                'o.year',
                'o.value',
            ]);

        $yearAxis = range($yearFrom, $yearTo);
        $seriesMap = [];

        foreach ($rows as $row) {
            $iso2 = strtoupper((string) $row->country_iso2);
            if (!isset($seriesMap[$iso2])) {
                $seriesMap[$iso2] = [
                    'key' => $iso2,
                    'label' => (string) $row->country_name,
                    'points_map' => [],
                ];
            }
            $seriesMap[$iso2]['points_map'][(int) $row->year] = (float) $row->value;
        }

        $series = [];
        foreach ($seriesMap as $item) {
            $points = [];
            foreach ($yearAxis as $year) {
                $points[] = [
                    'year' => $year,
                    'value' => $item['points_map'][$year] ?? null,
                ];
            }
            $series[] = [
                'key' => $item['key'],
                'label' => $item['label'],
                'points' => $points,
            ];
        }

        return $this->buildComparisonPayload(
            $indicator,
            'country',
            $yearFrom,
            $yearTo,
            'avg',
            $yearAxis,
            $series
        );
    }

    /**
     * @param  array<int, string>  $continents
     * @return array<string, mixed>
     */
    private function buildContinentComparison(
        WorldBankIndicator $indicator,
        array $continents,
        int $yearFrom,
        int $yearTo,
        string $aggregation
    ): array {
        $aggregationMode = strtolower($aggregation) === 'sum' ? 'sum' : 'avg';

        $selectedContinents = collect($continents)
            ->map(function (string $continent): string {
                return trim($continent);
            })
            ->filter(function (string $continent): bool {
                return $continent !== '';
            })
            ->unique()
            ->values()
            ->all();

        if (empty($selectedContinents)) {
            $selectedContinents = $this->getWorldBankContinents();
        }

        $aggregateSql = $aggregationMode === 'sum' ? 'SUM(o.value)' : 'AVG(o.value)';

        $rows = DB::table('world_bank_indicator_observations as o')
            ->join('world_bank_countries as c', 'c.iso2_code', '=', 'o.country_iso2')
            ->where('o.world_bank_indicator_id', $indicator->id)
            ->whereBetween('o.year', [$yearFrom, $yearTo])
            ->whereIn('c.continent', $selectedContinents)
            ->where('c.is_aggregate', false)
            ->whereNotNull('o.value')
            ->groupBy('c.continent', 'o.year')
            ->orderBy('o.year')
            ->orderBy('c.continent')
            ->selectRaw("c.continent as continent, o.year as year, {$aggregateSql} as value")
            ->get();

        $yearAxis = range($yearFrom, $yearTo);
        $seriesMap = [];

        foreach ($rows as $row) {
            $continent = (string) $row->continent;
            if (!isset($seriesMap[$continent])) {
                $seriesMap[$continent] = [
                    'key' => $continent,
                    'label' => $continent,
                    'points_map' => [],
                ];
            }
            $seriesMap[$continent]['points_map'][(int) $row->year] = $row->value !== null
                ? (float) $row->value
                : null;
        }

        $series = [];
        foreach ($seriesMap as $item) {
            $points = [];
            foreach ($yearAxis as $year) {
                $points[] = [
                    'year' => $year,
                    'value' => $item['points_map'][$year] ?? null,
                ];
            }
            $series[] = [
                'key' => $item['key'],
                'label' => $item['label'],
                'points' => $points,
            ];
        }

        return $this->buildComparisonPayload(
            $indicator,
            'continent',
            $yearFrom,
            $yearTo,
            $aggregationMode,
            $yearAxis,
            $series
        );
    }

    /**
     * @param  array<int, int>  $years
     * @param  array<int, array<string, mixed>>  $series
     * @return array<string, mixed>
     */
    private function buildComparisonPayload(
        WorldBankIndicator $indicator,
        string $mode,
        int $yearFrom,
        int $yearTo,
        string $aggregation,
        array $years,
        array $series
    ): array {
        return [
            'indicator' => [
                'id' => $indicator->wb_indicator_id,
                'name' => $indicator->name,
                'unit' => $indicator->unit,
                'topics' => $indicator->topics->pluck('name')->values()->all(),
            ],
            'compare_mode' => $mode,
            'aggregation' => $aggregation,
            'year_from' => $yearFrom,
            'year_to' => $yearTo,
            'years' => $years,
            'series' => $series,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array{0:int,1:int}
     */
    private function normalizeYearRange(int $yearFrom, int $yearTo): array
    {
        if ($yearFrom > $yearTo) {
            [$yearFrom, $yearTo] = [$yearTo, $yearFrom];
        }

        $currentYear = (int) now()->year;
        $yearFrom = max(1960, min($yearFrom, $currentYear));
        $yearTo = max(1960, min($yearTo, $currentYear));

        return [$yearFrom, $yearTo];
    }

    private function resolveCountryIso2ByName(string $countryName): ?string
    {
        $normalizedCountryName = Str::lower(trim($countryName));
        if ($normalizedCountryName === '') {
            return null;
        }

        $direct = WorldBankCountry::query()
            ->where('is_aggregate', false)
            ->whereRaw('LOWER(name) = ?', [$normalizedCountryName])
            ->value('iso2_code');

        if (is_string($direct) && strlen(trim($direct)) === 2) {
            return strtoupper(trim($direct));
        }

        $aliases = [
            'democratic republic of the congo' => 'Congo, Dem. Rep.',
            'cote d ivoire' => "Cote d'Ivoire",
            'côte d\'ivoire' => "Cote d'Ivoire",
            'cape verde' => 'Cabo Verde',
            'swaziland' => 'Eswatini',
            'sao tome and principe' => 'Sao Tome and Principe',
            'gambia' => 'Gambia, The',
            'congo' => 'Congo, Rep.',
        ];

        $aliasName = $aliases[$normalizedCountryName] ?? null;
        if ($aliasName) {
            $aliasIso2 = WorldBankCountry::query()
                ->where('is_aggregate', false)
                ->where('name', $aliasName)
                ->value('iso2_code');

            if (is_string($aliasIso2) && strlen(trim($aliasIso2)) === 2) {
                return strtoupper(trim($aliasIso2));
            }
        }

        $memberStateIso2 = AuMemberState::query()
            ->whereRaw('LOWER(name) = ?', [$normalizedCountryName])
            ->value('code_alpha2');

        if (is_string($memberStateIso2) && strlen(trim($memberStateIso2)) === 2) {
            return strtoupper(trim($memberStateIso2));
        }

        $fallback = WorldBankCountry::query()
            ->where('is_aggregate', false)
            ->where('name', 'like', '%' . trim($countryName) . '%')
            ->value('iso2_code');

        return (is_string($fallback) && strlen(trim($fallback)) === 2)
            ? strtoupper(trim($fallback))
            : null;
    }

    private function formatWorldBankValue(float $value, ?string $unit): string
    {
        $unitLabel = trim((string) $unit);

        if ($unitLabel !== '') {
            if (str_contains(strtolower($unitLabel), 'percent') || str_contains($unitLabel, '%')) {
                return number_format($value, 2) . '%';
            }
        }

        if (abs($value) >= 1_000_000_000) {
            return number_format($value / 1_000_000_000, 2) . 'B';
        }

        if (abs($value) >= 1_000_000) {
            return number_format($value / 1_000_000, 2) . 'M';
        }

        return number_format($value, 2);
    }

    private function buildImfMetrics(int $seed): array
    {
        return [
            [
                'label' => 'GDP Growth (annual %)',
                'value' => $this->formatPercent($this->scaledValue($seed, -1.8, 8.4, 5), true),
            ],
            [
                'label' => 'Inflation, consumer prices (%)',
                'value' => $this->formatPercent($this->scaledValue($seed, 1.3, 21.5, 11)),
            ],
            [
                'label' => 'Current Account Balance (% of GDP)',
                'value' => $this->formatPercent($this->scaledValue($seed, -15.0, 6.0, 17), true),
            ],
        ];
    }

    private function buildWorldBankMetrics(int $seed): array
    {
        return [
            [
                'label' => 'Population (millions)',
                'value' => number_format($this->scaledValue($seed, 0.35, 260.0, 3), 2),
            ],
            [
                'label' => 'Unemployment, total (% labor force)',
                'value' => $this->formatPercent($this->scaledValue($seed, 1.8, 28.0, 9)),
            ],
            [
                'label' => 'CO2 emissions (metric tons per capita)',
                'value' => number_format($this->scaledValue($seed, 0.09, 17.5, 23), 2),
            ],
        ];
    }

    private function scaledValue(int $seed, float $min, float $max, int $divisor): float
    {
        $safeDivisor = max(1, $divisor);
        $normalized = fmod($seed / $safeDivisor, 1000.0) / 1000.0;

        return $min + (($max - $min) * $normalized);
    }

    private function formatPercent(float $value, bool $signed = false): string
    {
        $formatted = number_format($value, 2);

        if ($signed && $value > 0) {
            $formatted = '+' . $formatted;
        }

        return $formatted . '%';
    }
}
