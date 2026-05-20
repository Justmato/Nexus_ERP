<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@erp.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        $manager = User::firstOrCreate(
            ['email' => 'gerente@erp.local'],
            [
                'name' => 'Gerente Demo',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        $manager->assignRole('manager');
    }
}
