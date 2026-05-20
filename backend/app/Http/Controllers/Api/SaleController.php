<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Traits\Filterable;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    use Filterable;

    protected array $sortable = ['folio', 'sale_date', 'total', 'created_at'];

    public function __construct(protected SaleService $saleService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Sale::with(['customer', 'warehouse', 'user']);
        $this->applyFilters($query, $request, ['folio']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        return $this->success($query->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $sale = $this->saleService->create(
            $request->safe()->except('items'),
            $request->validated('items'),
            auth()->id()
        );

        return $this->success($sale, 'Venta registrada', 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        return $this->success($sale->load(['items.product', 'customer', 'warehouse', 'user']));
    }

    public function confirm(Sale $sale): JsonResponse
    {
        try {
            $sale = $this->saleService->confirm($sale, auth()->id());
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success($sale, 'Venta confirmada');
    }
}
