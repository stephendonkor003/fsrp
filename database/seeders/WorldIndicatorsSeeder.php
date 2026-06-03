<?php

namespace Database\Seeders;

use App\Models\FsrpComponent;
use App\Models\Indicator;
use App\Models\IndicatorLevel;
use App\Models\IndicatorUnit;
use App\Models\ReportingFrequency;
use App\Models\User;
use App\Models\WorldIndicatorSetting;
use Illuminate\Database\Seeder;

class WorldIndicatorsSeeder extends Seeder
{
    private const IMF_BASE_URL = 'https://www.imf.org/external/datamapper/api/v1';
    private const WORLD_BANK_BASE_URL = 'https://api.worldbank.org/v2';

    /**
     * Configure IMF and World Bank world indicators for the FSRP dashboard.
     */
    public function run(): void
    {
        $this->call(MEConfigurationSeeder::class);
        $this->call(FsrpTaxonomySeeder::class);

        $creator = User::where('email', 'amodonlimited@gmail.com')->value('id')
            ?? User::query()->value('id');

        $this->seedSettings($creator);
        $this->seedMeIndicators($creator);
        $this->call(WorldBankCatalogSeeder::class);
    }

    private function seedSettings(?string $creator): void
    {
        $settings = WorldIndicatorSetting::query()->firstOrNew([]);

        $settings->fill([
            'page_title' => 'Food Security Indicator Analytics',
            'page_intro' => 'Compare FSRP food-system resilience, macroeconomic, agriculture, and member-state indicators for Eastern and Southern Africa using IMF DataMapper and World Bank indicator sources.',
            'is_public_enabled' => true,
            'enabled_regions' => ['Africa'],
            'default_region' => 'Africa',
            'imf_source_enabled' => true,
            'world_bank_source_enabled' => true,
            'imf_api_base_url' => config('services.imf.datamapper_base_url', self::IMF_BASE_URL),
            'world_bank_api_base_url' => config('services.world_bank.base_url', self::WORLD_BANK_BASE_URL),
            'notes' => implode("\n", [
                'Default FSRP world-indicator sources seeded from IMF DataMapper and World Bank Indicators API.',
                'Public maps default to the Eastern and Southern Africa FSRP footprint within the Africa shapefile region.',
                'IMF default codes: NGDP_RPCH, PCPIPCH, BCA_NGDP, GGXWDG_NGDP.',
                'World Bank default codes: SP.POP.TOTL, NY.GDP.MKTP.CD, NY.GDP.PCAP.CD, SL.UEM.TOTL.ZS, AG.PRD.FOOD.XD, AG.LND.AGRI.ZS, NV.AGR.TOTL.ZS.',
                'World Bank catalog and observations are synced by WorldBankCatalogSeeder when network access is available.',
            ]),
            'created_by' => $settings->exists ? ($settings->created_by ?? $creator) : $creator,
            'updated_by' => $creator,
        ]);

        $settings->save();
    }

    private function seedMeIndicators(?string $creator): void
    {
        $outcomeLevelId = IndicatorLevel::where('name', 'Outcome')->value('id')
            ?? IndicatorLevel::query()->value('id');
        $annualFrequencyId = ReportingFrequency::where('code', 'ANNUAL')->value('id')
            ?? ReportingFrequency::where('name', 'Annual')->value('id')
            ?? ReportingFrequency::query()->value('id');

        $percentUnitId = IndicatorUnit::where('name', 'Percent')->value('id');
        $numberUnitId = IndicatorUnit::where('name', 'Number')->value('id');
        $usdUnitId = IndicatorUnit::where('name', 'USD')->value('id');

        $policyComponentId = FsrpComponent::where('code', 'C4')->value('id')
            ?? FsrpComponent::query()->value('id');
        $marketComponentId = FsrpComponent::where('code', 'C3')->value('id')
            ?? $policyComponentId;

        foreach ($this->indicatorRows($policyComponentId, $marketComponentId, $outcomeLevelId, $annualFrequencyId, $percentUnitId, $numberUnitId, $usdUnitId, $creator) as $row) {
            Indicator::updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function indicatorRows(
        ?string $policyComponentId,
        ?string $marketComponentId,
        ?string $outcomeLevelId,
        ?string $annualFrequencyId,
        ?string $percentUnitId,
        ?string $numberUnitId,
        ?string $usdUnitId,
        ?string $creator
    ): array {
        return [
            [
                'name' => 'IMF - Real GDP growth',
                'fsrp_component_id' => $policyComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $percentUnitId,
                'primary_source' => $this->source('external_system_connector', self::IMF_BASE_URL . '/NGDP_RPCH'),
                'definitions' => 'IMF DataMapper indicator NGDP_RPCH: real gross domestic product annual percent change.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Macroeconomic resilience context indicator from IMF DataMapper.',
                'created_by' => $creator,
            ],
            [
                'name' => 'IMF - Inflation, average consumer prices',
                'fsrp_component_id' => $policyComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $percentUnitId,
                'primary_source' => $this->source('external_system_connector', self::IMF_BASE_URL . '/PCPIPCH'),
                'definitions' => 'IMF DataMapper indicator PCPIPCH: average consumer price inflation annual percent change.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Tracks macro price pressure relevant to food-system resilience and purchasing power.',
                'created_by' => $creator,
            ],
            [
                'name' => 'IMF - Current account balance',
                'fsrp_component_id' => $policyComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $percentUnitId,
                'primary_source' => $this->source('external_system_connector', self::IMF_BASE_URL . '/BCA_NGDP'),
                'definitions' => 'IMF DataMapper indicator BCA_NGDP: current account balance as a percent of GDP.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Supports monitoring of external-sector pressure in FSRP countries.',
                'created_by' => $creator,
            ],
            [
                'name' => 'IMF - General government gross debt',
                'fsrp_component_id' => $policyComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $percentUnitId,
                'primary_source' => $this->source('external_system_connector', self::IMF_BASE_URL . '/GGXWDG_NGDP'),
                'definitions' => 'IMF DataMapper indicator GGXWDG_NGDP: general government gross debt as a percent of GDP.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Fiscal context indicator for public investment and resilience programming.',
                'created_by' => $creator,
            ],
            [
                'name' => 'World Bank - Population, total',
                'fsrp_component_id' => $policyComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $numberUnitId,
                'primary_source' => $this->source('external_system_connector', self::WORLD_BANK_BASE_URL . '/country/all/indicator/SP.POP.TOTL'),
                'definitions' => 'World Bank indicator SP.POP.TOTL: total population.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Population denominator for member-state and regional comparisons.',
                'created_by' => $creator,
            ],
            [
                'name' => 'World Bank - GDP, current US$',
                'fsrp_component_id' => $policyComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $usdUnitId,
                'primary_source' => $this->source('external_system_connector', self::WORLD_BANK_BASE_URL . '/country/all/indicator/NY.GDP.MKTP.CD'),
                'definitions' => 'World Bank indicator NY.GDP.MKTP.CD: GDP at current US dollars.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Economic scale indicator for country and regional context.',
                'created_by' => $creator,
            ],
            [
                'name' => 'World Bank - GDP per capita, current US$',
                'fsrp_component_id' => $policyComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $usdUnitId,
                'primary_source' => $this->source('external_system_connector', self::WORLD_BANK_BASE_URL . '/country/all/indicator/NY.GDP.PCAP.CD'),
                'definitions' => 'World Bank indicator NY.GDP.PCAP.CD: GDP per capita at current US dollars.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Economic welfare context indicator.',
                'created_by' => $creator,
            ],
            [
                'name' => 'World Bank - Unemployment, total',
                'fsrp_component_id' => $policyComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $percentUnitId,
                'primary_source' => $this->source('external_system_connector', self::WORLD_BANK_BASE_URL . '/country/all/indicator/SL.UEM.TOTL.ZS'),
                'definitions' => 'World Bank indicator SL.UEM.TOTL.ZS: unemployment as a percent of total labor force.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Employment and livelihood context indicator.',
                'created_by' => $creator,
            ],
            [
                'name' => 'World Bank - Food production index',
                'fsrp_component_id' => $marketComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $numberUnitId,
                'primary_source' => $this->source('external_system_connector', self::WORLD_BANK_BASE_URL . '/country/all/indicator/AG.PRD.FOOD.XD'),
                'definitions' => 'World Bank indicator AG.PRD.FOOD.XD: food production index.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Food-system production context indicator.',
                'created_by' => $creator,
            ],
            [
                'name' => 'World Bank - Agricultural land',
                'fsrp_component_id' => $marketComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $percentUnitId,
                'primary_source' => $this->source('external_system_connector', self::WORLD_BANK_BASE_URL . '/country/all/indicator/AG.LND.AGRI.ZS'),
                'definitions' => 'World Bank indicator AG.LND.AGRI.ZS: agricultural land as a percent of land area.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Land-use context indicator for agriculture and resilience planning.',
                'created_by' => $creator,
            ],
            [
                'name' => 'World Bank - Agriculture, forestry, and fishing value added',
                'fsrp_component_id' => $marketComponentId,
                'indicator_level_id' => $outcomeLevelId,
                'frequency_of_reporting_id' => $annualFrequencyId,
                'unit_id' => $percentUnitId,
                'primary_source' => $this->source('external_system_connector', self::WORLD_BANK_BASE_URL . '/country/all/indicator/NV.AGR.TOTL.ZS'),
                'definitions' => 'World Bank indicator NV.AGR.TOTL.ZS: agriculture, forestry, and fishing value added as a percent of GDP.',
                'methodology' => 'External API',
                'responsible_party' => 'FSRP M&E team',
                'notes' => 'Agriculture-sector contribution indicator for FSRP countries.',
                'created_by' => $creator,
            ],
        ];
    }

    private function source(string $type, string $value): string
    {
        return $type . '|' . $value;
    }
}
