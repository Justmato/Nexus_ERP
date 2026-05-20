<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected FolioGenerator $folioGenerator,
        protected ActivityLogService $activityLog
    ) {}

    public function create(array $data, array $items, int $userId): Purchase
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $purchase = Purchase::create([
                ...$data,
                'folio' => $this->folioGenerator->generate('COM', new Purchase),
                'user_id' => $userId,
                'status' => $data['status'] ?? 'draft',
            ]);

            $this->syncItems($purchase, $items);

            if (in_array($purchase->status, ['confirmed', 'received'])) {
                $this->processInventory($purchase, $userId);
            }

            $this->activityLog->log('created', 'purchases', $purchase, null, $userId);

            return $purchase->load(['items.product', 'supplier', 'warehouse']);
        });
    }

    public function receive(Purchase $purchase, int $userId): Purchase
    {
        return DB::transaction(function () use ($purchase, $userId) {
            $purchase->update(['status' => 'received']);
            $this->processInventory($purchase, $userId);
            $this->activityLog->log('received', 'purchases', $purchase, null, $userId);

            return $purchase->fresh(['items.product', 'supplier']);
        });
    }

    protected function syncItems(Purchase $purchase, array $items): void
    {
        $purchase->items()->delete();
        $subtotal = 0;
        $tax = 0;

        foreach ($items as $item) {
            $lineSubtotal = $item['quantity'] * $item['unit_cost'];
            $lineTax = $lineSubtotal * (($item['tax_rate'] ?? 16) / 100);
            $lineTotal = $lineSubtotal + $lineTax;

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'tax_rate' => $item['tax_rate'] ?? 16,
                'subtotal' => $lineSubtotal,
                'total' => $lineTotal,
            ]);

            $subtotal += $lineSubtotal;
            $tax += $lineTax;
        }

        $purchase->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
        ]);
    }

    protected function processInventory(Purchase $purchase, int $userId): void
    {
        foreach ($purchase->items as $item) {
            $product = $item->product;
            if ($product->track_stock) {
                $this->inventoryService->recordMovement(
                    $product,
                    $purchase->warehouse_id,
                    'in',
                    $item->quantity,
                    (float) $item->unit_cost,
                    Purchase::class,
                    $purchase->id,
                    $purchase->folio,
                    $userId
                );
            }
        }
    }
}
