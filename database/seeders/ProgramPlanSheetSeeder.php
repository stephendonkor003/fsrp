<?php

namespace Database\Seeders;

use App\Models\ProcurementGeographic;
use App\Models\ProcurementMethodPlanned;
use App\Models\ProcurementPlan;
use App\Models\ProcurementProgramPlan;
use App\Models\ProcurementStage;
use App\Models\ProcurementStatus;
use App\Models\ProcurementStepApproval;
use App\Models\ProcurementStepStage;
use App\Models\User;
use Illuminate\Database\Seeder;
class ProgramPlanSheetSeeder extends Seeder
{
    public function run(): void
    {
        // UUID migration: created_by is a FK to users.id (uuid), so don't hardcode "1".
        $createdBy = User::where('email', 'amodonlimited@gmail.com')->value('id')
            ?? User::query()->value('id');

        $method = ProcurementMethodPlanned::first();
        $stage = ProcurementStage::active()->ordered()->first();
        $status = ProcurementStatus::active()->orderBy('sort_order')->first();
        $geo = ProcurementGeographic::active()->first();
        $stepStage = ProcurementStepStage::active()->ordered()->first();
        $stepApproval = ProcurementStepApproval::with('governanceNode')
            ->where('is_active', true)
            ->orderBy('approval_order')
            ->first();

        $plans = [
            [
                'name' => 'FSRP Food Security Procurement Plan',
                'description' => 'Procurement plan for food security, market access, and resilience activities in Eastern and Southern Africa.'
            ],
            [
                'name' => 'FSRP Digital Early Warning Plan',
                'description' => 'Digital systems for food security monitoring, early warning, and program coordination.'
            ],
            [
                'name' => 'FSRP Regional Market Access Plan',
                'description' => 'Procurements supporting regional trade corridors, aggregation, storage, and logistics.'
            ],
            [
                'name' => 'FSRP Climate Resilience Procurement Plan',
                'description' => 'Climate adaptation procurements for water management, climate services, and resilient production.'
            ],
            [
                'name' => 'FSRP Capacity Strengthening Procurement Plan',
                'description' => 'Training, technical assistance, and safeguards procurements supporting FSRP delivery.'
            ],
        ];

        foreach ($plans as $planData) {
            $programPlan = ProcurementProgramPlan::updateOrCreate(
                ['name' => $planData['name']],
                [
                    'description' => $planData['description'],
                    'is_active' => true,
                    'created_by' => $createdBy,
                ]
            );

            $baseBudget = match ($planData['name']) {
                'FSRP Food Security Procurement Plan' => 1200000,
                'FSRP Digital Early Warning Plan' => 900000,
                'FSRP Regional Market Access Plan' => 750000,
                'FSRP Climate Resilience Procurement Plan' => 950000,
                default => 800000,
            };

            for ($i = 0; $i < 15; $i++) {
                $code = ProcurementPlan::generateCode(
                    $method?->method_abbr ?? 'CS',
                    $geo?->abbr ?? 'CQS'
                );

                $startDate = now()->addDays(30 * $i);
                $endDate = $startDate->copy()->addDays(45 + ($i % 5) * 5);
                $title = "{$planData['name']} Batch #" . ($i + 1);
                $budget = $baseBudget + ($i * 20000);

                ProcurementPlan::updateOrCreate(
                    ['procurement_code' => $code],
                    [
                        'is_code_auto_generated' => true,
                        'title' => $title,
                        'description' => $planData['description'],
                        'program_plan_id' => $programPlan->id,
                        'method_planned_id' => $method?->id,
                        'geographic_id' => $geo?->id,
                        'stage_id' => $stage?->id,
                        'status_id' => $status?->id,
                        'step_stage_id' => $stepStage?->id,
                        'step_approval_id' => $stepApproval?->id,
                        'is_launched' => $i % 3 !== 0,
                        'estimated_start_date' => $startDate,
                        'estimated_end_date' => $endDate,
                        'estimated_budget' => $budget,
                        'currency' => 'USD',
                        'fiscal_year' => 2026,
                        'created_by' => $createdBy,
                    ]
                );
            }
        }
    }
}
