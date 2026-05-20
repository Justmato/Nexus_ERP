<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Traits\Filterable;
use App\Models\Supplier;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use Filterable;

    protected array $sortable = ['name', 'code', 'created_at'];

    public function __construct(protected ActivityLogService $activityLog) {}

    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();
        $this->applyFilters($query, $request, ['name', 'code', 'email', 'tax_id']);

        return $this->success($query->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::create($request->validated());
        $this->activityLog->log('created', 'suppliers', $supplier);

        return $this->success($supplier, 'Proveedor creado', 201);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return $this->success($supplier);
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier->update($request->validated());
        $this->activityLog->log('updated', 'suppliers', $supplier);

        return $this->success($supplier, 'Proveedor actualizado');
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplier->delete();
        $this->activityLog->log('deleted', 'suppliers', $supplier);

        return $this->success(null, 'Proveedor eliminado');
    }
}
