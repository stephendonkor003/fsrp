<?php

namespace Database\Seeders;

use App\Models\Funder;
use App\Models\Program;
use App\Models\Project;
use App\Models\Sector;
use App\Models\Activity;
use App\Models\SubActivity;
use App\Models\ProgramFunding;
use App\Models\ProjectAllocation;
use App\Models\ActivityAllocation;
use App\Models\SubActivityAllocation;
use App\Models\AuMemberState;
use App\Models\AuRegionalBlock;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcurementStructureSeeder extends Seeder
{
    public function run(): void
    {
        // UUID migration: use a real user id instead of hardcoding "1".
        $createdBy = User::where('email', 'amodonlimited@gmail.com')->value('id')
            ?? User::query()->value('id');

        $sectors = collect([
            'Food Security & Resilience',
            'Climate Resilience',
            'Regional Market Access',
            'Climate-Smart Agriculture',
            'Seed Systems',
            'Water & Irrigation',
            'Monitoring & Evaluation',
            'Safeguards & Inclusion'
        ])->map(function ($name) {
            return Sector::updateOrCreate(
                ['name' => $name],
                ['description' => "{$name} sector portfolio"]
            );
        })->values();

        $programNames = [
            'FSRP Food Security and Resilience Program',
            'Climate-Smart Production Support',
            'Regional Food Market Integration',
            'Digital Early Warning and Data Systems',
            'Seed Systems and Input Access',
            'Irrigation and Water Management',
            'Post-Harvest Loss Reduction',
            'Nutrition-Sensitive Value Chains',
            'Safeguards and Community Engagement',
            'Program Coordination and Learning',
        ];

        $programs = collect($programNames)->map(function ($name, $index) use ($sectors, $createdBy) {
            $sector = $sectors[$index % $sectors->count()];
            return Program::updateOrCreate(
                ['name' => $name],
                [
                    'program_id' => "PRG-" . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'sector_id' => $sector->id,
                    'description' => "Program focused on {$name}",
                    'currency' => 'USD',
                    'start_year' => 2025,
                    'end_year' => 2029,
                    'total_years' => 5,
                    'total_budget' => 1000000 + ($index * 250000),
                    'created_by' => $createdBy,
                ]
            );
        })->values();

        $projectNames = [
            'Food security response coordination',
            'Regional market information rollout',
            'Climate early warning modernization',
            'Seed multiplication and certification support',
            'Digital extension and advisory services',
            'Solar irrigation expansion',
        ];

        $projects = collect($projectNames)->map(function ($projectName, $index) use ($programs, $createdBy) {
            $program = $programs[$index % $programs->count()];
            return Project::updateOrCreate(
                ['project_id' => "PRJ-" . str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'program_id' => $program->id,
                    'name' => $projectName,
                    'description' => "Project supporting {$program->name}",
                    'currency' => 'USD',
                    'start_year' => 2025,
                    'end_year' => 2027,
                    'total_years' => 3,
                    'total_budget' => 750000 + ($index * 50000),
                    'created_by' => $createdBy,
                ]
            );
        })->values();

        $activities = collect(range(1, 14))->map(function ($i) use ($projects, $createdBy) {
            $project = $projects[($i - 1) % $projects->count()];
            return Activity::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'name' => "Activity {$i} for {$project->name}",
                ],
                [
                    'description' => "Support task {$i} for the {$project->name}",
                    'governance_node_id' => null,
                    'created_by' => $createdBy,
                ]
            );
        })->values();

        collect(range(1, 20))->each(function ($i) use ($activities, $createdBy) {
            $activity = $activities[($i - 1) % $activities->count()];
            SubActivity::updateOrCreate(
                [
                    'activity_id' => $activity->id,
                    'name' => "Sub Activity {$i} of {$activity->name}",
                ],
                [
                    'description' => "Detailing sub-task {$i}.",
                    'created_by' => $createdBy,
                ]
            );
        });

        $allocationYears = range(2025, 2027);
        $projects->each(function ($project) use ($allocationYears) {
            $yearNumber = 1;
            foreach ($allocationYears as $year) {
                ProjectAllocation::updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'year' => $yearNumber,
                    ],
                    [
                        'year_number' => $yearNumber,
                        'actual_year' => $year,
                        'amount' => max(50000, round($project->total_budget / count($allocationYears), 2)),
                    ]
                );
                $yearNumber++;
            }
        });

        $activities->each(function ($activity) use ($allocationYears) {
            foreach ($allocationYears as $year) {
                ActivityAllocation::updateOrCreate(
                    [
                        'activity_id' => $activity->id,
                        'year' => $year,
                    ],
                    [
                        'amount' => 25000 + ($year % 3) * 5000,
                    ]
                );
            }
        });

        SubActivity::all()->each(function ($subActivity) use ($allocationYears) {
            foreach ($allocationYears as $year) {
                SubActivityAllocation::updateOrCreate(
                    [
                        'sub_activity_id' => $subActivity->id,
                        'year' => $year,
                    ],
                    [
                        'amount' => 10000 + ($year % 2) * 2500,
                    ]
                );
            }
        });

        $funders = Funder::orderBy('id')->take(7)->get();
        if ($funders->count() < 7) {
            $this->command->warn('Less than 7 funding partners exist; please run FundingPartnerSeeder first.');
            return;
        }

        $memberStateIds = AuMemberState::pluck('id')->take(15)->toArray();
        $regionalBlockIds = AuRegionalBlock::pluck('id')->take(15)->toArray();

        $programIndex = 0;
        $fundingCounter = 0;
        foreach ($funders as $funder) {
            for ($i = 0; $i < 5; $i++) {
                $program = $programs[$programIndex % $programs->count()];
                $funding = ProgramFunding::updateOrCreate(
                    [
                        'funder_id' => $funder->id,
                        'program_id' => $program->id,
                        'start_year' => 2025 + $i,
                    ],
                    [
                        'program_name' => $program->name,
                        'funding_type' => 300000 + ($i * 50000),
                        'approved_amount' => 300000 + ($i * 50000),
                        'currency' => 'USD',
                        'status' => 'approved',
                        'start_year' => 2025 + $i,
                        'end_year' => 2027 + $i,
                        'created_by' => $createdBy,
                    ]
                );

                if ($fundingCounter % 2 === 0) {
                    $this->syncPivotWithUuids($funding->memberStates(), $memberStateIds, false);
                } else {
                    $this->syncPivotWithUuids($funding->regionalBlocks(), $regionalBlockIds, false);
                }

                $fundingCounter++;
                $programIndex++;
            }
        }
    }

    private function syncPivotWithUuids($relation, array $ids, bool $detaching = true): void
    {
        $cleanIds = collect($ids)->filter()->unique()->values();

        if ($cleanIds->isEmpty()) {
            if ($detaching) {
                $relation->sync([]);
            }

            return;
        }

        $existing = DB::table($relation->getTable())
            ->where($relation->getForeignPivotKeyName(), $relation->getParent()->getKey())
            ->whereIn($relation->getRelatedPivotKeyName(), $cleanIds)
            ->pluck('id', $relation->getRelatedPivotKeyName());

        $payload = $cleanIds->mapWithKeys(
            fn ($id) => [$id => ['id' => (string) ($existing[$id] ?? Str::uuid())]]
        )->toArray();

        $relation->sync($payload, $detaching);
    }
}
