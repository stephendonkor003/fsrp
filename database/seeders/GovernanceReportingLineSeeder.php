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
            ['child' => 'African Union Commission', 'parent' => 'Assembly', 'type' => 'primary'],
            ['child' => 'Department of Health', 'parent' => 'African Union Commission', 'type' => 'primary'],
            ['child' => 'Department of Trade', 'parent' => 'African Union Commission', 'type' => 'primary'],
            ['child' => 'Directorate of Governance', 'parent' => 'Department of Health', 'type' => 'primary'],
            ['child' => 'Directorate of Elections', 'parent' => 'Department of Trade', 'type' => 'primary'],
            ['child' => 'Program Operations Unit', 'parent' => 'Directorate of Governance', 'type' => 'primary'],
            ['child' => 'Consortia Alpha', 'parent' => 'African Union Commission', 'type' => 'dotted'],
            ['child' => 'Think Tank A', 'parent' => 'Consortia Alpha', 'type' => 'dotted'],
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
