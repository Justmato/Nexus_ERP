<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'api'],
            ['description' => 'Administrador con acceso total']
        );
        $admin->syncPermissions(Permission::where('guard_name', 'api')->pluck('name'));

        $manager = Role::firstOrCreate(
            ['name' => 'manager', 'guard_name' => 'api'],
            ['description' => 'Gerente operativo']
        );
        $manager->syncPermissions([
            'dashboard.view', 'products.view', 'products.create', 'products.update',
            'inventory.view', 'sales.view', 'sales.create', 'sales.confirm',
            'purchases.view', 'purchases.create', 'purchases.receive',
            'customers.view', 'customers.create', 'customers.update',
            'suppliers.view', 'suppliers.create', 'reports.view', 'activity.view',
        ]);

        $sales = Role::firstOrCreate(
            ['name' => 'vendedor', 'guard_name' => 'api'],
            ['description' => 'Vendedor']
        );
        $sales->syncPermissions([
            'dashboard.view', 'products.view', 'sales.view', 'sales.create', 'sales.confirm',
            'customers.view', 'customers.create',
        ]);
    }
}
