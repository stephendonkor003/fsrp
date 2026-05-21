<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GovernanceLevel;

class GovernanceLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['key' => 'organ', 'name' => 'Organ', 'sort_order' => 1, 'description' => 'AU institution'],
            ['key' => 'commission', 'name' => 'Commission', 'sort_order' => 2, 'description' => 'Executive/secretariat'],
            ['key' => 'department', 'name' => 'Department', 'sort_order' => 3, 'description' => 'Policy cluster'],
            ['key' => 'directorate', 'name' => 'Directorate', 'sort_order' => 4, 'description' => 'Technical policy unit'],
            ['key' => 'division_unit', 'name' => 'Division / Unit', 'sort_order' => 5, 'description' => 'Operational staff'],
            ['key' => 'consortia', 'name' => 'Consortia', 'sort_order' => 6, 'description' => 'Consortia groupings'],
            ['key' => 'think_tanks', 'name' => 'Think Tanks', 'sort_order' => 7, 'description' => 'Think tank entities'],
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
