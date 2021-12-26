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
        Permission::firstOrCreate(['name'=>'user-list'  ,   'guard_name' =>   'api']);
        Permission::firstOrCreate(['name'=>'user-create',   'guard_name' =>   'api']);
        Permission::firstOrCreate(['name'=>'user-update',   'guard_name' =>   'api']);
        Permission::firstOrCreate(['name'=>'user-delete',   'guard_name' =>   'api']);

        // Roles Permissions
        Permission::firstOrCreate(['name'=> 'role-list'  ,   'guard_name' =>   'api']);
        Permission::firstOrCreate(['name'=> 'role-create',   'guard_name' =>   'api']);
        Permission::firstOrCreate(['name'=> 'role-update',   'guard_name' =>   'api']);
        Permission::firstOrCreate(['name'=> 'role-delete',   'guard_name' =>   'api']);

        //Permission of Permissions
        Permission::firstOrCreate(['name'=> 'permission-list',   'guard_name' =>   'api']);
    }
}
