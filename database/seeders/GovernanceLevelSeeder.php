<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GovernanceLevel;

class GovernanceLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['key' => 'program', 'name' => 'FSRP Program', 'sort_order' => 1, 'description' => 'Overall Food System Resilience Program governance'],
            ['key' => 'regional_coordination', 'name' => 'Regional Coordination Unit', 'sort_order' => 2, 'description' => 'Eastern and Southern Africa regional coordination'],
            ['key' => 'component', 'name' => 'Program Component', 'sort_order' => 3, 'description' => 'FSRP technical component'],
            ['key' => 'national_piu', 'name' => 'National Project Implementation Unit', 'sort_order' => 4, 'description' => 'Country implementation and fiduciary unit'],
            ['key' => 'implementing_partner', 'name' => 'Implementing Partner', 'sort_order' => 5, 'description' => 'Delivery partner or consortium'],
            ['key' => 'field_site', 'name' => 'Field Site / Community', 'sort_order' => 6, 'description' => 'Local implementation area'],
        ];

        foreach ($levels as $level) {
            GovernanceLevel::updateOrCreate(
                ['key' => $level['key']],
                [
                    'name' => $level['name'],
                    'sort_order' => $level['sort_order'],
                    'description' => $level['description'],
                ]
            );
        }
    }
}
