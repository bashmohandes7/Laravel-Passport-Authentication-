<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // User Permissions
        Permission::firstOrCreate(['name'=>'admin-list'  ,   'guard_name' =>   'admin']);
        Permission::firstOrCreate(['name'=>'admin-create',   'guard_name' =>   'admin']);
        Permission::firstOrCreate(['name'=>'admin-update',   'guard_name' =>   'admin']);
        Permission::firstOrCreate(['name'=>'admin-delete',   'guard_name' =>   'admin']);

        // Roles Permissions
        Permission::firstOrCreate(['name'=> 'role-list'  ,   'guard_name' =>   'admin']);
        Permission::firstOrCreate(['name'=> 'role-create',   'guard_name' =>   'admin']);
        Permission::firstOrCreate(['name'=> 'role-update',   'guard_name' =>   'admin']);
        Permission::firstOrCreate(['name'=> 'role-delete',   'guard_name' =>   'admin']);

        //Permission of Permissions
        Permission::firstOrCreate(['name'=> 'permission-list',   'guard_name' =>   'admin']);
    }
}
