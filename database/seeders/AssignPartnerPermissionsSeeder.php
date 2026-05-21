<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class AssignPartnerPermissionsSeeder extends Seeder
{
    /**
     * Run the database migrations.
     */
    public function run(): void
    {
        $this->command->info('🔍 Finding Funding Partner role...');

        // Get the Funding Partner role
        $partnerRole = Role::where('name', 'Funding Partner')->first();

        if (!$partnerRole) {
            $this->command->error('❌ Funding Partner role not found! Run PartnerPortalPermissionsSeeder first.');
            return;
        }

        $this->command->info("✅ Found role: {$partnerRole->name} (ID: {$partnerRole->id})");

        // Get all partner permissions (excluding admin-only permissions)
        $partnerPermissions = Permission::where('name', 'like', 'partner.%')
            ->whereNotIn('name', ['partner.requests.manage', 'partner.requests.respond'])
            ->pluck('name')
            ->toArray();

        $this->command->info('📋 Partner permissions: ' . implode(', ', $partnerPermissions));

        // Find all funding partner users
        $fundingPartners = User::where('user_type', 'funding_partner')
            ->orWhereHas('funderPortal')
            ->get();

        if ($fundingPartners->isEmpty()) {
            $this->command->warn('⚠️  No funding partner users found.');
            return;
        }

        $this->command->info("👥 Found {$fundingPartners->count()} funding partner user(s)");
        $this->command->newLine();

        $updated = 0;
        $alreadySet = 0;

        foreach ($fundingPartners as $user) {
            $this->command->info("Processing: {$user->name} ({$user->email})");

            $needsUpdate = false;

            // Check if user has the correct role
            if ($user->role_id !== $partnerRole->id) {
                $this->command->warn("  ↳ Updating role from {$user->role_id} to {$partnerRole->id}");
                $user->role_id = $partnerRole->id;
                $user->save();
                $needsUpdate = true;
            } else {
                $this->command->info("  ↳ Role already correct");
            }

            // Ensure user_type is set correctly
            if ($user->user_type !== 'funding_partner') {
                $this->command->warn("  ↳ Updating user_type to 'funding_partner'");
                $user->user_type = 'funding_partner';
                $user->save();
                $needsUpdate = true;
            }

            // Verify permissions through role
            $userPermissions = $user->role->permissions->pluck('name')->toArray();
            $missingPermissions = array_diff($partnerPermissions, $userPermissions);

            if (!empty($missingPermissions)) {
                $this->command->warn("  ↳ Missing permissions: " . implode(', ', $missingPermissions));
                $this->command->warn("  ↳ These should be added to the Funding Partner role!");
            } else {
                $this->command->info("  ↳ All permissions present via role");
            }

            if ($needsUpdate) {
                $updated++;
                $this->command->info("  ✅ Updated successfully");
            } else {
                $alreadySet++;
                $this->command->info("  ✅ Already configured correctly");
            }

            $this->command->newLine();
        }

        // Summary
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('Summary:');
        $this->command->info("  Total funding partners: {$fundingPartners->count()}");
        $this->command->info("  Updated: {$updated}");
        $this->command->info("  Already correct: {$alreadySet}");
        $this->command->info('═══════════════════════════════════════');
        $this->command->newLine();

        // Verify role permissions
        $this->command->info('🔍 Verifying Funding Partner role permissions...');
        $rolePermissions = $partnerRole->permissions->pluck('name')->toArray();

        $rolePartnerPerms = array_filter($rolePermissions, function($perm) {
            return str_starts_with($perm, 'partner.') &&
                   !in_array($perm, ['partner.requests.manage', 'partner.requests.respond']);
        });

        $missingFromRole = array_diff($partnerPermissions, $rolePartnerPerms);

        if (!empty($missingFromRole)) {
            $this->command->error('❌ The Funding Partner role is missing these permissions:');
            foreach ($missingFromRole as $perm) {
                $this->command->error("  - {$perm}");
            }
            $this->command->newLine();
            $this->command->warn('⚠️  Run the following command to fix:');
            $this->command->warn('   php artisan db:seed --class=PartnerPortalPermissionsSeeder');
        } else {
            $this->command->info('✅ Funding Partner role has all required permissions');
        }

        $this->command->newLine();
        $this->command->info('✨ Seeder completed successfully!');
    }
}
