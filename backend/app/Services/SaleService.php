<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected FolioGenerator $folioGenerator,
        protected ActivityLogService $activityLog
    ) {}

    public function create(array $data, array $items, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $sale = Sale::create([
                ...$data,
                'folio' => $this->folioGenerator->generate('VTA', new Sale),
                'user_id' => $userId,
                'status' => $data['status'] ?? 'draft',
            ]);

            $this->syncItems($sale, $items);

            if ($sale->status === 'confirmed') {
                $this->processInventory($sale, $userId);
            }

            $this->activityLog->log('created', 'sales', $sale, null, $userId);

            return $sale->load(['items.product', 'customer', 'warehouse']);
        });
    }

    public function confirm(Sale $sale, int $userId): Sale
    {
        return DB::transaction(function () use ($sale, $userId) {
            if ($sale->status === 'confirmed') {
                return $sale;
            }

            $sale->update(['status' => 'confirmed']);
            $this->processInventory($sale, $userId);
            $this->activityLog->log('confirmed', 'sales', $sale, null, $userId);

            return $sale->fresh(['items.product', 'customer']);
        });
    }

    protected function syncItems(Sale $sale, array $items): void
    {
        $sale->items()->delete();
        $subtotal = 0;
        $tax = 0;

        foreach ($items as $item) {
            $lineSubtotal = $item['quantity'] * $item['unit_price'] - ($item['discount'] ?? 0);
            $lineTax = $lineSubtotal * (($item['tax_rate'] ?? 16) / 100);
            $lineTotal = $lineSubtotal + $lineTax;

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'tax_rate' => $item['tax_rate'] ?? 16,
                'subtotal' => $lineSubtotal,
                'total' => $lineTotal,
            ]);

            $subtotal += $lineSubtotal;
            $tax += $lineTax;
        }

        $sale->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax - ($sale->discount ?? 0),
        ]);
    }

    protected function processInventory(Sale $sale, int $userId): void
    {
        foreach ($sale->items as $item) {
            $product = $item->product;
            if ($product->track_stock) {
                $this->inventoryService->recordMovement(
                    $product,
                    $sale->warehouse_id,
                    'out',
                    $item->quantity,
                    (float) $product->cost_price,
                    Sale::class,
                    $sale->id,
                    $sale->folio,
                    $userId
                );
            }
        }
    }
}
