<?php

namespace Database\Seeders;

use App\Models\AuMemberState;
use Illuminate\Database\Seeder;

class AuMemberStateSeeder extends Seeder
{
    /**
     * Seed only the FSRP countries that report data to this platform.
     */
    public function run(): void
    {
        $memberStates = [
            ['name' => 'Comoros', 'code' => 'COM', 'code_alpha2' => 'KM', 'region_name' => 'Eastern Africa', 'flag_path' => 'assets/images/member-states/flags/comoros.svg'],
            ['name' => 'Kenya', 'code' => 'KEN', 'code_alpha2' => 'KE', 'region_name' => 'Eastern Africa', 'flag_path' => 'assets/images/member-states/flags/kenya.svg'],
            ['name' => 'Malawi', 'code' => 'MWI', 'code_alpha2' => 'MW', 'region_name' => 'Southern Africa', 'flag_path' => 'assets/images/member-states/flags/malawi.svg'],
            ['name' => 'Mozambique', 'code' => 'MOZ', 'code_alpha2' => 'MZ', 'region_name' => 'Southern Africa', 'flag_path' => 'assets/images/member-states/flags/mozambique.svg'],
            ['name' => 'Somalia', 'code' => 'SOM', 'code_alpha2' => 'SO', 'region_name' => 'Eastern Africa', 'flag_path' => 'assets/images/member-states/flags/somalia.svg'],
            ['name' => 'Ethiopia', 'code' => 'ETH', 'code_alpha2' => 'ET', 'region_name' => 'Eastern Africa', 'flag_path' => 'assets/images/member-states/flags/ethiopia.svg'],
            ['name' => 'Tanzania', 'code' => 'TZA', 'code_alpha2' => 'TZ', 'region_name' => 'Eastern Africa', 'flag_path' => 'assets/images/member-states/flags/tanzania.svg'],
        ];

        foreach ($memberStates as $index => $state) {
            AuMemberState::updateOrCreate(
                ['name' => $state['name']],
                [
                    'code' => $state['code'],
                    'code_alpha2' => $state['code_alpha2'],
                    'region_name' => $state['region_name'],
                    'flag_path' => $state['flag_path'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        AuMemberState::query()
            ->whereNotIn('name', array_column($memberStates, 'name'))
            ->delete();
    }
}
