<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Permissions
    $permissions = [
        'create student',
        'view student',
        'edit student',
        'delete student',
        'create staff',
        'view staff'
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    // Global role
    Role::firstOrCreate(['name' => 'SuperAdmin']);

    // Default tenant roles
    Role::firstOrCreate(['name' => 'SchoolOwner']);
    Role::firstOrCreate(['name' => 'SchoolAdmin']);

    // Assign permissions to SchoolOwner
    $ownerRole = Role::where('name', 'SchoolOwner')->first();
    $ownerRole->syncPermissions($permissions);

    // Assign limited permissions to SchoolAdmin
    $adminRole = Role::where('name', 'SchoolAdmin')->first();
    $adminRole->syncPermissions([
        'create student',
        'view student'
    ]);
    }
}
