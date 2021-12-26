<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create User Admin And Assign All Permissions to you

        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name'=>'api']);
        $role->syncPermissions(Permission::all());
        $data['name'] = 'super-admin';
        $data['email'] = 'super_admin@admin.com';
        $data['password'] = Hash::make('123456');
        $admin = User::firstOrCreate(['email' => $data['email']], $data);
        $admin->assignRole($role);
    }
}
