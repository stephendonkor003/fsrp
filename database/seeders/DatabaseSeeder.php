<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a fresh FSRP administrative portal baseline.
     *
     * This intentionally avoids old demo/legacy imports and only creates
     * the access-control catalog needed for a clean installation.
     */

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            AuMasterDataPermissionsSeeder::class,
            PartnerPortalPermissionsSeeder::class,
            ConsortiumOperationsPermissionsSeeder::class,
            HrGovernancePermissionsSeeder::class,
            ProcurementPermissionsSeeder::class,
            FsrpTaxonomySeeder::class,
            FsrpEventNewsSeeder::class,
            MasterAdminSeeder::class,
        ]);
    }
}
