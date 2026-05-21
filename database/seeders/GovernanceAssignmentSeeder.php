<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\GovernanceNode;
use App\Models\GovernanceNodeAssignment;

class GovernanceAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id', 'email');
        $nodes = GovernanceNode::pluck('id', 'name');

        $assignments = [
            [
                'email' => 'admin@example.com',
                'node' => 'African Union Commission',
                'role_title' => 'Commission Secretary',
                'is_primary' => true,
            ],
            [
                'email' => 'finance.manager@example.com',
                'node' => 'Department of Trade',
                'role_title' => 'Finance Manager',
                'is_primary' => true,
            ],
            [
                'email' => 'finance.officer@example.com',
                'node' => 'Department of Trade',
                'role_title' => 'Finance Officer',
                'is_primary' => false,
            ],
            [
                'email' => 'budget.officer@example.com',
                'node' => 'Department of Health',
                'role_title' => 'Budget Officer',
                'is_primary' => true,
            ],
            [
                'email' => 'hr.manager@example.com',
                'node' => 'African Union Commission',
                'role_title' => 'HR Manager',
                'is_primary' => true,
            ],
            [
                'email' => 'prescreener@example.com',
                'node' => 'Directorate of Governance',
                'role_title' => 'Prescreening Evaluator',
                'is_primary' => true,
            ],
            [
                'email' => 'evaluator@example.com',
                'node' => 'Directorate of Elections',
                'role_title' => 'Evaluation Evaluator',
                'is_primary' => true,
            ],
        ];

        foreach ($assignments as $assignment) {
            $userId = $users[$assignment['email']] ?? null;
            $nodeId = $nodes[$assignment['node']] ?? null;

            if (!$userId || !$nodeId) {
                continue;
            }

            GovernanceNodeAssignment::updateOrCreate(
                [
                    'user_id' => $userId,
                    'node_id' => $nodeId,
                    'role_title' => $assignment['role_title'],
                ],
                [
                    'is_primary' => $assignment['is_primary'],
                ]
            );
        }
    }
}
