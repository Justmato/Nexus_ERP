<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->user = User::where('email', 'admin@erp.local')->first();
    }

    public function test_can_list_products(): void
    {
        $token = auth('api')->login($this->user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/products');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_can_create_product(): void
    {
        $token = auth('api')->login($this->user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/products', [
                'sku' => 'TEST-001',
                'name' => 'Producto Test',
                'unit' => 'pza',
                'sale_price' => 100,
                'cost_price' => 50,
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('products', ['sku' => 'TEST-001']);
    }
}
