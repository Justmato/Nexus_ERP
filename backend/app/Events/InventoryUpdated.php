<?php

namespace App\Events;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Product $product,
        public InventoryMovement $movement
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('inventory')];
    }

    public function broadcastAs(): string
    {
        return 'inventory.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'product_id' => $this->product->id,
            'sku' => $this->product->sku,
            'stock' => (float) $this->product->stock,
            'movement' => [
                'type' => $this->movement->type,
                'quantity' => (float) $this->movement->quantity,
                'balance_after' => (float) $this->movement->balance_after,
            ],
        ];
    }
}
