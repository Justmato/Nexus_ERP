<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['dashboard.view', 'dashboard'],
            ['products.view', 'products'], ['products.create', 'products'], ['products.update', 'products'], ['products.delete', 'products'],
            ['inventory.view', 'inventory'],
            ['sales.view', 'sales'], ['sales.create', 'sales'], ['sales.confirm', 'sales'],
            ['purchases.view', 'purchases'], ['purchases.create', 'purchases'], ['purchases.receive', 'purchases'],
            ['customers.view', 'customers'], ['customers.create', 'customers'], ['customers.update', 'customers'], ['customers.delete', 'customers'],
            ['suppliers.view', 'suppliers'], ['suppliers.create', 'suppliers'], ['suppliers.update', 'suppliers'], ['suppliers.delete', 'suppliers'],
            ['reports.view', 'reports'],
            ['roles.manage', 'roles'],
            ['activity.view', 'activity'],
        ];

        foreach ($permissions as [$name, $module]) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'api'],
                ['module' => $module]
            );
        }
    }
}
