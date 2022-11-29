<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Traits\HasRoles;

class RolesandPermissionsTableSeeder extends Seeder
{
    use HasRoles;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Permission::create([
            'name' => 'allow_permission_create',
            'guard_name' => 'web'
        ]);
        Permission::create([
            'name' => 'allow_permission_update',
            'guard_name' => 'web'
        ]);
        Permission::create([
            'name' => 'allow_permission_delete',
            'guard_name' => 'web'        
        ]);
        Permission::create([
            'name' => 'allow_role_create',
            'guard_name' => 'web'
        ]);
        Permission::create([
            'name' => 'allow_role_update',
            'guard_name' => 'web'
        ]);
        Permission::create([
            'name' => 'allow_role_delete',
            'guard_name' => 'web'
        ]);

        Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ])->givePermissionTo(Permission::all());
        Role::create([
            'name' => 'qc',
            'guard_name' => 'web'
        ]);
        Role::create([
            'name' => 'technician',
            'guard_name' => 'web'
        ]);
        Role::create([
            'name' => 'cashier',
            'guard_name' => 'web'
        ]);
    }
}
