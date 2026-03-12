<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Make sure role exists
        $role = Role::firstOrCreate([
            'name' => 'SuperAdmin',
            'guard_name' => 'web'
        ]);

        // Create super admin user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@multischool.com'],
            [
                'fullname' => 'Platform Super Admin',
                'role' => 'super-admin',
                'password' => Hash::make('Admin@123'),
                'school_id' => null,
                'is_active' => true,
            ]
        );

        Permission::firstOrCreate([
    'name' => 'manage schools',
    'guard_name' => 'web'
]);
$role = Role::where('name', 'SuperAdmin')->first();
$role->givePermissionTo('manage schools');

        $user->assignRole($role);
    }
}