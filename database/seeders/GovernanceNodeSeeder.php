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
            ['key' => 'organ', 'name' => 'Assembly', 'code' => 'ORG-ASM', 'description' => 'AU Assembly'],
            ['key' => 'commission', 'name' => 'African Union Commission', 'code' => 'COM-AUC', 'description' => 'AUC Secretariat'],
            ['key' => 'department', 'name' => 'Department of Health', 'code' => 'DEP-HLT', 'description' => 'Health, Humanitarian Affairs & Social Development'],
            ['key' => 'department', 'name' => 'Department of Trade', 'code' => 'DEP-TRD', 'description' => 'Economic Development, Trade, Industry & Mining'],
            ['key' => 'directorate', 'name' => 'Directorate of Governance', 'code' => 'DIR-GOV', 'description' => 'Governance & Constitutionalism'],
            ['key' => 'directorate', 'name' => 'Directorate of Elections', 'code' => 'DIR-ELC', 'description' => 'Elections'],
            ['key' => 'division_unit', 'name' => 'Program Operations Unit', 'code' => 'UNT-OPS', 'description' => 'Operational delivery'],
            ['key' => 'consortia', 'name' => 'Consortia Alpha', 'code' => 'CON-A', 'description' => 'Consortia group'],
            ['key' => 'think_tanks', 'name' => 'Think Tank A', 'code' => 'TT-A', 'description' => 'Think tank entity'],
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
