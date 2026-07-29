<?php

    namespace Modules\Journals\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Illuminate\Support\Str;
    use Modules\Usermanagement\Models\PermissionGroup;
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;

    class PermissionSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run()
        {
            $data = $this->data();

            foreach ($data as $value) {
                $group = PermissionGroup::updateOrCreate([
                    'name'       => $value['name'],
                    'slug'       => Str::slug($value['name'])
                ]);

                foreach ($this->crudActions($value['name']) as $action) {
                    $output[] = ['name' => $action, 'group' => $group->id];
                }

                foreach ($output as $value) {
                    $permission = Permission::updateOrCreate([
                        'name'       => $value['name'],
                        'guard_name' => 'web' // or 'api
                    ], [
                        'permission_group_id' => $value['group']
                    ]);

                    $roles = Role::all();
                    foreach ($roles as $role) {
                        $role->givePermissionTo($permission);
                    }

                }
            }
        }

        public function data()
        {
            return [
                ['name' => 'journals']
            ];
        }

        public function crudActions($name)
        {
            $actions = [];
            // list of permission actions
            $crud = ['create', 'read', 'update', 'delete','export', 'authorize', 'report','restore'];


            foreach ($crud as $value) {
                $actions[] = $name . '.' . $value;
            }

            return $actions;
        }
    }
