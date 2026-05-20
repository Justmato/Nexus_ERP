<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\Filterable;
use App\Models\InventoryMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    use Filterable;

    protected array $sortable = ['movement_date', 'created_at'];

    public function index(Request $request): JsonResponse
    {
        $query = InventoryMovement::with(['product', 'warehouse', 'user']);
        $this->applyFilters($query, $request, ['folio']);

        if ($productId = $request->get('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($from = $request->get('date_from')) {
            $query->whereDate('movement_date', '>=', $from);
        }

        if ($to = $request->get('date_to')) {
            $query->whereDate('movement_date', '<=', $to);
        }

        $query->orderByDesc('movement_date');

        return $this->success($query->paginate($request->integer('per_page', 20)));
    }
}
