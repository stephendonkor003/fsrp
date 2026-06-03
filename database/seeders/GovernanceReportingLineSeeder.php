<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GovernanceNode;
use App\Models\GovernanceReportingLine;

class GovernanceReportingLineSeeder extends Seeder
{
    public function run(): void
    {
        $nodes = GovernanceNode::pluck('id', 'name');

        $lines = [
            ['child' => 'FSRP Regional Coordination Unit', 'parent' => 'Food System Resilience Program', 'type' => 'primary'],
            ['child' => 'Food Security and Resilience Component', 'parent' => 'FSRP Regional Coordination Unit', 'type' => 'primary'],
            ['child' => 'Climate-Smart Agriculture Component', 'parent' => 'FSRP Regional Coordination Unit', 'type' => 'primary'],
            ['child' => 'Member State PIU - Eastern Africa', 'parent' => 'Food Security and Resilience Component', 'type' => 'primary'],
            ['child' => 'Member State PIU - Southern Africa', 'parent' => 'Climate-Smart Agriculture Component', 'type' => 'primary'],
            ['child' => 'Regional Implementation Partner', 'parent' => 'FSRP Regional Coordination Unit', 'type' => 'dotted'],
            ['child' => 'Priority Value Chain Field Site', 'parent' => 'Regional Implementation Partner', 'type' => 'dotted'],
        ];

        foreach ($lines as $line) {
            $childId = $nodes[$line['child']] ?? null;
            $parentId = $nodes[$line['parent']] ?? null;

            if (!$childId || !$parentId) {
                continue;
            }

            GovernanceReportingLine::updateOrCreate(
                [
                    'child_node_id' => $childId,
                    'parent_node_id' => $parentId,
                    'line_type' => $line['type'],
                ],
                [
                    'effective_start' => null,
                    'effective_end' => null,
                ]
            );
        }
    }
}
