<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            ['name' => 'view-audits', 'description' => 'View audits'],
            ['name' => 'create-audits', 'description' => 'Create new audits'],
            ['name' => 'edit-audits', 'description' => 'Edit existing audits'],
            ['name' => 'delete-audits', 'description' => 'Delete audits'],
            ['name' => 'manage-templates', 'description' => 'Manage audit templates'],
            ['name' => 'manage-users', 'description' => 'Manage users and permissions'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }

        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with full access']
        );

        $auditorRole = Role::firstOrCreate(
            ['name' => 'auditor'],
            ['description' => 'Auditor who can create and edit audits']
        );

        $viewerRole = Role::firstOrCreate(
            ['name' => 'viewer'],
            ['description' => 'Viewer who can only view audits']
        );

        // Assign permissions to roles
        $adminRole->permissions()->sync(Permission::all());
        $auditorRole->permissions()->sync(
            Permission::whereIn('name', ['view-audits', 'create-audits', 'edit-audits'])->get()
        );
        $viewerRole->permissions()->sync(
            Permission::where('name', 'view-audits')->get()
        );

        // Create default admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@audit-demo.local'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role to the admin user
        $adminUser->roles()->sync([$adminRole->id]);

        $this->command->info('Roles, permissions, and admin user created successfully!');
        $this->command->info('Admin credentials: admin@audit-demo.local / password');
    }
}
