<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = ['admin.access', 'users.manage', 'roles.manage', 'tasks.manage', 'pages.view', 'pages.create', 'pages.update', 'pages.publish', 'pages.scope.all', 'services.manage', 'inquiries.manage', 'settings.manage', 'testimonials.manage'];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach ([
            'Owner' => $permissions,
            'Super Admin' => $permissions,
            'Admin' => array_diff($permissions, ['users.manage', 'roles.manage']),
            'Website Manager' => ['admin.access', 'pages.view', 'pages.create', 'pages.update', 'pages.publish', 'pages.scope.all', 'services.manage', 'settings.manage', 'testimonials.manage'],
            'Page Editor' => ['admin.access', 'pages.view', 'pages.update'],
            'Page Publisher' => ['admin.access', 'pages.view', 'pages.publish'],
            'Sales Manager' => ['admin.access', 'inquiries.manage'],
        ] as $name => $grants) {
            Role::findOrCreate($name, 'web')->syncPermissions($grants);
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
