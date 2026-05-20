<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Traits\Filterable;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    use Filterable;

    protected array $sortable = ['folio', 'purchase_date', 'total', 'created_at'];

    public function __construct(protected PurchaseService $purchaseService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Purchase::with(['supplier', 'warehouse', 'user']);
        $this->applyFilters($query, $request, ['folio']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        return $this->success($query->paginate($request->integer('per_page', 15)));
    }

    public function store(StorePurchaseRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->create(
            $request->safe()->except('items'),
            $request->validated('items'),
            auth()->id()
        );

        return $this->success($purchase, 'Compra registrada', 201);
    }

    public function show(Purchase $purchase): JsonResponse
    {
        return $this->success($purchase->load(['items.product', 'supplier', 'warehouse', 'user']));
    }

    public function receive(Purchase $purchase): JsonResponse
    {
        $purchase = $this->purchaseService->receive($purchase, auth()->id());

        return $this->success($purchase, 'Compra recibida en inventario');
    }
}
