<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'System Admin' => 'Full FSRP system administrator',
            'HR Manager' => 'Human resource manager',
            'HR Officer' => 'Human resource officer',
            'Finance Manager' => 'Finance and fiduciary manager',
            'Finance Officer' => 'Finance and disbursement officer',
            'Budget Officer' => 'Budget planning and allocation officer',
            'Auditor' => 'Audit and compliance reviewer',
            'Prescreening Evaluator' => 'Prescreening and eligibility evaluator',
            'Evaluation Evaluator' => 'Technical evaluation reviewer',
            'Communication Officer' => 'Program communications officer',
            'Communications Officer' => 'Program communications officer',
            'Member State Focal Point' => 'Country focal point user',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }
    }
}
