<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a fresh FSRP administrative portal baseline.
     *
     * This intentionally avoids old demo/legacy imports and only creates
     * the access-control, AU master-data, and FSRP public-content baseline.
     */

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            AuMasterDataPermissionsSeeder::class,
            AuAgenda2063Seeder::class,
            AuFlagshipProjectSeeder::class,
            AuMemberStateSeeder::class,
            AuRegionalBlockSeeder::class,
            PartnerPortalPermissionsSeeder::class,
            FundingPartnerSeeder::class,
            AssignPartnerPermissionsSeeder::class,
            HrGovernancePermissionsSeeder::class,
            ProcurementPermissionsSeeder::class,
            FsrpTaxonomySeeder::class,
            FsrpEventNewsSeeder::class,
            FsrpGalleryMediaSeeder::class,
            WorldIndicatorsSeeder::class,
            MasterAdminSeeder::class,
        ]);
    }
}
