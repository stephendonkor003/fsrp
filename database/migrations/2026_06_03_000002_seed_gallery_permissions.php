<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permissions = [
            'gallery.manage' => [
                'module' => 'communications',
                'description' => 'Upload and manage public gallery images and videos',
            ],
            'gallery.approve' => [
                'module' => 'communications',
                'description' => 'Approve and publish public gallery media',
            ],
        ];

        foreach ($permissions as $name => $attributes) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'id' => DB::table('permissions')->where('name', $name)->value('id') ?: (string) Str::uuid(),
                    'module' => $attributes['module'],
                    'description' => $attributes['description'],
                    'updated_at' => now(),
                    'created_at' => DB::table('permissions')->where('name', $name)->value('created_at') ?: now(),
                ]
            );
        }

        if (! Schema::hasTable('roles') || ! Schema::hasTable('role_permission')) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_keys($permissions))
            ->pluck('id')
            ->all();

        $roleIds = DB::table('roles')
            ->whereIn('name', ['System Admin', 'Communication Officer', 'Communications Officer'])
            ->pluck('id')
            ->all();

        foreach ($roleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                $exists = DB::table('role_permission')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (! $exists) {
                    DB::table('role_permission')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', ['gallery.manage', 'gallery.approve'])
            ->pluck('id')
            ->all();

        if (Schema::hasTable('role_permission') && $permissionIds !== []) {
            DB::table('role_permission')->whereIn('permission_id', $permissionIds)->delete();
        }

        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
