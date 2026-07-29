<?php

namespace Modules\Journals\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Usermanagement\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class JournalsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Journals Permission Group exists
        $group = PermissionGroup::firstOrCreate(
            ['name' => 'journals'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // 2. Generate standard CRUD & export permissions
        $actions = ['create', 'read', 'update', 'delete', 'export'];
        foreach ($actions as $action) {
            $permissionName = 'journals.' . $action;
            $permission = Permission::firstOrCreate([
                'name'       => $permissionName,
                'guard_name' => 'web',
            ], [
                'permission_group_id' => $group->id,
            ]);

            // Assign to Administrator role by default
            $adminRoles = Role::whereIn('name', ['administrator', 'superadmin'])->get();
            foreach ($adminRoles as $role) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
