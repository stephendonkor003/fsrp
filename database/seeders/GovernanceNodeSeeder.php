<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GovernanceLevel;
use App\Models\GovernanceNode;

class GovernanceNodeSeeder extends Seeder
{
    public function run(): void
    {
        $levels = GovernanceLevel::pluck('id', 'key');

        $nodes = [
            ['key' => 'program', 'name' => 'Food System Resilience Program', 'code' => 'FSRP', 'description' => 'FSRP for Eastern and Southern Africa'],
            ['key' => 'regional_coordination', 'name' => 'FSRP Regional Coordination Unit', 'code' => 'FSRP-RCU', 'description' => 'Regional coordination and implementation support'],
            ['key' => 'component', 'name' => 'Food Security and Resilience Component', 'code' => 'FSRP-C1', 'description' => 'Food security, production, and resilience activities'],
            ['key' => 'component', 'name' => 'Climate-Smart Agriculture Component', 'code' => 'FSRP-C2', 'description' => 'Climate-smart agriculture and risk management activities'],
            ['key' => 'national_piu', 'name' => 'Member State PIU - Eastern Africa', 'code' => 'PIU-EA', 'description' => 'Eastern Africa national implementation coordination'],
            ['key' => 'national_piu', 'name' => 'Member State PIU - Southern Africa', 'code' => 'PIU-SA', 'description' => 'Southern Africa national implementation coordination'],
            ['key' => 'implementing_partner', 'name' => 'Regional Implementation Partner', 'code' => 'IP-REG', 'description' => 'Partner supporting regional FSRP delivery'],
            ['key' => 'field_site', 'name' => 'Priority Value Chain Field Site', 'code' => 'FS-VC', 'description' => 'Field-level food system resilience implementation site'],
        ];

        foreach ($nodes as $node) {
            $levelId = $levels[$node['key']] ?? null;
            if (!$levelId) {
                continue;
            }

            GovernanceNode::updateOrCreate(
                ['name' => $node['name'], 'level_id' => $levelId],
                [
                    'code' => $node['code'],
                    'description' => $node['description'],
                    'status' => 'active',
                ]
            );
        }
    }
}
