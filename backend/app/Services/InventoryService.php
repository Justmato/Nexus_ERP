<?php

namespace App\Services;

use App\Events\InventoryUpdated;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function recordMovement(
        Product $product,
        int $warehouseId,
        string $type,
        float $quantity,
        float $unitCost = 0,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $folio = null,
        ?int $userId = null,
        ?string $notes = null
    ): InventoryMovement {
        return DB::transaction(function () use (
            $product, $warehouseId, $type, $quantity, $unitCost,
            $referenceType, $referenceId, $folio, $userId, $notes
        ) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            $balanceBefore = (float) $product->stock;
            $avgCostBefore = (float) $product->cost_price;

            $signedQty = in_array($type, ['in']) ? abs($quantity) : -abs($quantity);
            $balanceAfter = $balanceBefore + $signedQty;

            if ($product->track_stock && $balanceAfter < 0) {
                throw new \RuntimeException("Stock insuficiente para el producto {$product->sku}");
            }

            $avgCostAfter = $this->calculateAverageCost(
                $balanceBefore, $avgCostBefore, $signedQty, $unitCost, $type
            );

            if ($type === 'in' && $signedQty > 0) {
                $product->cost_price = $avgCostAfter;
            }

            $product->stock = $balanceAfter;
            $product->save();

            $movement = InventoryMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouseId,
                'user_id' => $userId,
                'type' => $type,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'folio' => $folio,
                'quantity' => $signedQty,
                'unit_cost' => $unitCost,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'avg_cost_before' => $avgCostBefore,
                'avg_cost_after' => $avgCostAfter,
                'notes' => $notes,
                'movement_date' => now(),
            ]);

            event(new InventoryUpdated($product, $movement));

            return $movement;
        });
    }

    protected function calculateAverageCost(
        float $balanceBefore,
        float $avgCostBefore,
        float $signedQty,
        float $unitCost,
        string $type
    ): float {
        if ($type !== 'in' || $signedQty <= 0) {
            return $avgCostBefore;
        }

        $newBalance = $balanceBefore + $signedQty;
        if ($newBalance <= 0) {
            return $unitCost;
        }

        return (($balanceBefore * $avgCostBefore) + ($signedQty * $unitCost)) / $newBalance;
    }
}
