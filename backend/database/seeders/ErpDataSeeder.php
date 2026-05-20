<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ErpDataSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::firstOrCreate(
            ['code' => 'ALM-01'],
            ['name' => 'Almacén Principal', 'is_default' => true, 'is_active' => true]
        );

        $categories = ['Electrónica', 'Oficina', 'Herramientas', 'Consumibles'];
        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }

        $products = [
            ['SKU-001', 'Laptop Pro 15"', 12000, 15999, 5],
            ['SKU-002', 'Monitor 27" 4K', 4500, 6999, 10],
            ['SKU-003', 'Teclado Mecánico', 800, 1299, 20],
            ['SKU-004', 'Mouse Inalámbrico', 350, 599, 30],
            ['SKU-005', 'Resma Papel A4', 120, 189, 50],
            ['SKU-006', 'Toner Negro', 450, 799, 15],
            ['SKU-007', 'Taladro Industrial', 2800, 4299, 8],
            ['SKU-008', 'Cable HDMI 2m', 80, 149, 100],
        ];

        $categoryIds = Category::pluck('id')->toArray();
        foreach ($products as $i => [$sku, $name, $cost, $sale, $stock]) {
            Product::firstOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'category_id' => $categoryIds[$i % count($categoryIds)],
                    'cost_price' => $cost,
                    'sale_price' => $sale,
                    'stock' => $stock,
                    'min_stock' => max(2, (int) ($stock * 0.2)),
                    'unit' => 'pza',
                    'track_stock' => true,
                    'is_active' => true,
                ]
            );
        }

        for ($i = 1; $i <= 10; $i++) {
            Customer::firstOrCreate(
                ['code' => 'CLI-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => fake()->company(),
                    'email' => fake()->companyEmail(),
                    'phone' => fake()->phoneNumber(),
                    'tax_id' => 'RFC'.fake()->numerify('########'),
                    'city' => fake()->city(),
                    'is_active' => true,
                ]
            );
        }

        for ($i = 1; $i <= 5; $i++) {
            Supplier::firstOrCreate(
                ['code' => 'PROV-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => fake()->company().' S.A.',
                    'email' => fake()->companyEmail(),
                    'contact_name' => fake()->name(),
                    'payment_terms' => 30,
                    'is_active' => true,
                ]
            );
        }
    }
}
