<?php

namespace Database\Seeders;

use App\Models\FsrpComponent;
use App\Models\FsrpSubcomponent;
use Illuminate\Database\Seeder;

class FsrpTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            [
                'code' => 'C1',
                'name' => '(Re-)Building Resilient Productive Capacity',
                'description' => 'Regional coordination for climate-smart agricultural technology, advisory services, post-harvest loss mitigation, and productive capacity rebuilding.',
                'auc_allocation_usd' => 4000000,
                'sort_order' => 10,
                'subcomponents' => [
                    ['code' => 'C1.1', 'name' => 'Climate-smart technologies, practices, and policy options'],
                    ['code' => 'C1.2', 'name' => 'Post-harvest food loss mitigation, storage, and cold storage'],
                    ['code' => 'C1.3', 'name' => 'Agricultural R&D, extension, advisory services, knowledge platforms, conferences, and digital extension'],
                ],
            ],
            [
                'code' => 'C2',
                'name' => 'Sustainable Natural Resources in Agricultural Landscapes',
                'description' => 'Regional support for sustainable land and natural resource management in food-system landscapes.',
                'auc_allocation_usd' => 0,
                'sort_order' => 20,
                'subcomponents' => [
                    ['code' => 'C2.1', 'name' => 'Soil health and sustainable land management coordination'],
                ],
            ],
            [
                'code' => 'C3',
                'name' => 'Getting to Market',
                'description' => 'Trade policy harmonization, food and trade standards, food safety, and trade negotiation capacity.',
                'auc_allocation_usd' => 3000000,
                'sort_order' => 30,
                'subcomponents' => [
                    ['code' => 'C3.1', 'name' => 'Trade policy harmonization, food standards, food safety, and compliance'],
                    ['code' => 'C3.2', 'name' => 'Trade negotiation capacity for member states'],
                ],
            ],
            [
                'code' => 'C4',
                'name' => 'Food Systems Resilience in Policy',
                'description' => 'Evidence-based policy planning, NAIP support, climate-resilience strategy, and regional coordination.',
                'auc_allocation_usd' => 4000000,
                'sort_order' => 40,
                'subcomponents' => [
                    ['code' => 'C4.1', 'name' => 'Evidence-based planning and NAIP reviews/new NAIPs'],
                    ['code' => 'C4.2', 'name' => 'Strategy development, AUC business planning, climate resilience, and CSA'],
                    ['code' => 'C4.3', 'name' => 'Coordination and delivery'],
                ],
            ],
            [
                'code' => 'C5',
                'name' => 'Contingent Emergency Response Component (CERC)',
                'description' => 'Emergency preparedness and activation arrangements for eligible crisis response.',
                'auc_allocation_usd' => 0,
                'sort_order' => 50,
                'subcomponents' => [
                    ['code' => 'C5.1', 'name' => 'Emergency preparedness and response activation'],
                ],
            ],
            [
                'code' => 'C6',
                'name' => 'Project Management',
                'description' => 'Project coordination, financial management, procurement, M&E, reporting, audit, and safeguards.',
                'auc_allocation_usd' => 2000000,
                'sort_order' => 60,
                'subcomponents' => [
                    ['code' => 'C6.1', 'name' => 'Project coordination, FM, procurement, M&E, reporting, audit, and safeguards'],
                ],
            ],
        ];

        foreach ($components as $componentData) {
            $subcomponents = $componentData['subcomponents'];
            unset($componentData['subcomponents']);

            $component = FsrpComponent::updateOrCreate(
                ['code' => $componentData['code']],
                $componentData + ['is_active' => true]
            );

            foreach ($subcomponents as $index => $subcomponentData) {
                FsrpSubcomponent::updateOrCreate(
                    ['code' => $subcomponentData['code']],
                    [
                        'component_id' => $component->id,
                        'name' => $subcomponentData['name'],
                        'description' => $subcomponentData['description'] ?? null,
                        'sort_order' => ($index + 1) * 10,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
